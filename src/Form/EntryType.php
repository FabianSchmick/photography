<?php

namespace App\Form;

use App\Entity\Entry;
use App\Entity\Location;
use App\Entity\Tag;
use App\Service\CoreService;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class EntryType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    /**
     * @var CoreService
     */
    private $coreService;

    /**
     * @var Security
     */
    private $security;

    /**
     * EntryType constructor.
     */
    public function __construct(EntityManagerInterface $em, CoreService $coreService, Security $security)
    {
        $this->em = $em;
        $this->coreService = $coreService;
        $this->security = $security;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Entry $entry */
        $entry = $options['data'];

        $builder
            ->add('name')
            ->add('image', EntryImageType::class, [
                'required' => $entry->getImage() ? false : true,
                'placeholder_text' => $entry->getImage() ? $entry->getImage()->getOriginalName() : 'label.no_file_selected',
            ])
            ->add('description', TextareaType::class, [
                'required' => false,
            ])
            ->add('author', EntityType::class, [
                'required' => false,
                'class' => 'App:User',
                'data' => $entry->getAuthor() ?? $this->security->getUser(),
                'placeholder' => '',
                'query_builder' => function (EntityRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('u')
                        ->orderBy('u.fullname', 'ASC');
                },
            ])
            ->add('location', EntityType::class, [
                'required' => false,
                'class' => 'App:Location',
                'placeholder' => '',
                'query_builder' => function (EntityRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('l')
                        ->orderBy('l.name', 'ASC');
                },
                'attr' => [
                    'class' => 'select2-add',
                ],
            ])
            ->add('timestamp', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
            ])
            ->add('tags', EntityType::class, [
                'class' => 'App:Tag',
                'query_builder' => function (EntityRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('t')
                        ->orderBy('t.sort', 'DESC');
                },
                'multiple' => true,
                'attr' => [
                    'class' => 'select2-add',
                ],
            ])
            ->add('tour', EntityType::class, [
                'required' => false,
                'class' => 'App:Tour',
                'placeholder' => '',
                'query_builder' => function (EntityRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('t')
                        ->orderBy('t.name', 'ASC');
                },
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();

            if (!empty($data['location'] = trim($data['location']))) {
                $data['location'] = $this->coreService->saveNewEntityByName($data['location'], Location::class, 'App:Location');
            }

            if (empty($data['timestamp'])) {
                $data['timestamp'] = date('yyyy-MM-dd');
            }

            foreach ($data['tags'] as $key => $tag) {
                if (!empty($tag = trim($tag))) {
                    $data['tags'][$key] = $this->coreService->saveNewEntityByName($tag, Tag::class, 'App:Tag');
                }
            }

            $event->setData($data);
        });

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            /** @var Entry $entry */
            $entry = $event->getForm()->getData();

            if (!$entry->getTour() && $tour = $entry->getPreviewTour()) {
                $tour->setPreviewEntry(null);
                $this->em->persist($tour);
            }

            $date = $entry->getTimestamp();
            $date->setTime(date('H'), date('i'), date('s'));
            $entry->setTimestamp($date);

            $entry->setDescription($this->coreService->purifyString($entry->getDescription()));
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Entry::class,
        ]);
    }
}
