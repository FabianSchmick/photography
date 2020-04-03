<?php

namespace App\Form;

use App\Entity\Tag;
use App\Service\CoreService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TagType extends AbstractType
{
    /**
     * @var CoreService
     */
    private $coreService;

    /**
     * TagType constructor.
     */
    public function __construct(CoreService $coreService)
    {
        $this->coreService = $coreService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Tag $tag */
        $tag = $options['data'];

        $builder
            ->add('name')
            ->add('description', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'wysiwyg',
                ],
            ])
            ->add('sort', NumberType::class, [
                'required' => false,
            ])
        ;

        if (!$tag->getEntries()->isEmpty()) {
            $builder->add('previewEntry', EntityType::class, [
                'required' => false,
                'class' => 'App:Entry',
                'choices' => $tag->getEntries(),
                'placeholder' => '',
                'attr' => [
                    'class' => 'select2 form-control',
                ],
            ]);
        }

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            /** @var Tag $tag */
            $tag = $event->getForm()->getData();

            $tag->setDescription($this->coreService->purifyString($tag->getDescription()));
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tag::class,
        ]);
    }
}
