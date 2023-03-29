<?php

namespace App\Form;

use App\Entity\Location;
use App\Entity\Post;
use App\Entity\Tag;
use App\Entity\Tour;
use App\Entity\User;
use App\Form\Custom\ExtendableEntityByNameType;
use App\Form\Custom\PurifyTextareaType;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class PostType extends AbstractType
{
    public function __construct(private readonly EntityManagerInterface $em, private readonly Security $security)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Post $post */
        $post = $options['data'];

        $builder
            ->add('name')
            ->add('image', PostImageType::class, [
                'required' => !$post->getImage(),
                'placeholder_text' => $post->getImage() ? $post->getImage()->getOriginalName() : 'label.no_file_selected',
                'label_attr' => ['class' => 'required'],
            ])
            ->add('description', PurifyTextareaType::class, [
                'required' => false,
            ])
            ->add('author', EntityType::class, [
                'required' => false,
                'class' => User::class,
                'data' => $post->getAuthor() ?? $this->security->getUser(),
                'placeholder' => '',
                'query_builder' => fn (EntityRepository $er): QueryBuilder => $er->createQueryBuilder('u')
                    ->orderBy('u.fullname', 'ASC'),
            ])
            ->add('location', ExtendableEntityByNameType::class, [
                'required' => false,
                'class' => Location::class,
            ])
            ->add('timestamp', DateType::class, [
                'required' => false,
                'widget' => 'single_text',
            ])
            ->add('tags', ExtendableEntityByNameType::class, [
                'class' => Tag::class,
                'multiple' => true,
            ])
            ->add('tour', EntityType::class, [
                'required' => false,
                'class' => Tour::class,
                'placeholder' => '',
                'query_builder' => fn (EntityRepository $er): QueryBuilder => $er->createQueryBuilder('t')
                    ->orderBy('t.name', 'ASC'),
            ])
        ;

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();

            if (empty($data['timestamp'])) {
                $data['timestamp'] = date('yyyy-MM-dd');
            }

            $event->setData($data);
        });

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            /** @var Post $post */
            $post = $event->getForm()->getData();

            if (!$post->getTour() && $tour = $post->getPreviewTour()) {
                $tour->setPreviewPost(null);
                $this->em->persist($tour);
            }

            $date = $post->getTimestamp();
            $date->setTime(date('H'), date('i'), date('s'));
            $post->setTimestamp($date);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Post::class,
        ]);
    }
}
