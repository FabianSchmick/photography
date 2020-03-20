<?php

namespace App\Form;

use App\Entity\Author;
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
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

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
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * EntryType constructor.
     */
    public function __construct(EntityManagerInterface $em, TranslatorInterface $translator, CoreService $coreService)
    {
        $this->em = $em;
        $this->coreService = $coreService;
        $this->translator = $translator;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('name', TextType::class, [
                'label' => 'name',
            ])
            ->add('description', TextareaType::class, [
                'label' => 'description',
                'required' => false,
                'attr' => [
                    'class' => 'wysiwyg',
                ],
            ])
            ->add('author', EntityType::class, [
                'label' => 'author',
                'required' => false,
                'class' => 'App:Author',
                'placeholder' => '',
                'query_builder' => function (EntityRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('a')
                        ->orderBy('a.name', 'ASC');
                },
                'attr' => [
                    'class' => 'select2 select2-add form-control',
                ],
            ])
            ->add('image', EntryImageType::class, [
                'label' => 'image',
                'required' => false,
                'placeholder_text' => $options['data']->getImage() ? $options['data']->getImage()->getOriginalName() : $this->translator->trans('no.file.selected'),
            ])
            ->add('location', EntityType::class, [
                'label' => 'location',
                'required' => false,
                'class' => 'App:Location',
                'placeholder' => '',
                'query_builder' => function (EntityRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('l')
                        ->orderBy('l.name', 'ASC');
                },
                'attr' => [
                    'class' => 'select2 select2-add form-control',
                ],
            ])
            ->add('timestamp', DateType::class, [
                'label' => 'timestamp',
                'required' => false,
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
            ])
            ->add('tags', EntityType::class, [
                'label' => 'tags',
                'class' => 'App:Tag',
                'query_builder' => function (EntityRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('t')
                        ->orderBy('t.sort', 'DESC');
                },
                'multiple' => true,
                'attr' => [
                    'class' => 'select2 select2-add form-control',
                ],
            ])
            ->add('tour', EntityType::class, [
                'label' => 'tour',
                'required' => false,
                'class' => 'App:Tour',
                'placeholder' => '',
                'query_builder' => function (EntityRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('t')
                        ->orderBy('t.name', 'ASC');
                },
                'attr' => [
                    'class' => 'select2 form-control',
                ],
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();

            if (!empty($data['author'] = trim($data['author']))) {
                $data['author'] = $this->saveNewChoiceByName($data['author'], Author::class, 'App:Author');
            }

            if (!empty($data['location'] = trim($data['location']))) {
                $data['location'] = $this->saveNewChoiceByName($data['location'], Location::class, 'App:Location');
            }

            if (empty($data['timestamp'])) {
                $data['timestamp'] = date('yyyy-MM-dd');
            }

            foreach ($data['tags'] as $key => $tag) {
                if (!empty($tag = trim($tag))) {
                    $data['tags'][$key] = $this->saveNewChoiceByName($tag, Tag::class, 'App:Tag');
                }
            }

            $event->setData($data);
        });

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $object = $form->getData();

            /** @var \DateTime $date */
            $date = $object->getTimestamp();
            $date->setTime(date('H'), date('i'), date('s'));
            $object->setTimestamp($date);

            $object->setDescription($this->coreService->purifyString($object->getDescription()));
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Entry::class,
        ]);
    }

    protected function saveNewChoiceByName($choice, $class, $repo): int
    {
        $entity = $this->em->getRepository($repo)->find($choice);

        if (!$entity) {
            $entity = new $class();
            $entity->setName($choice);
            $this->em->persist($entity);
            $this->em->flush();
        }

        return $entity->getId();
    }
}
