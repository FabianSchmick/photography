<?php

namespace App\Form;

use App\Entity\Tour;
use App\Service\CoreService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

class TourType extends AbstractType
{
    /**
     * @var CoreService
     */
    private $coreService;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * TourType constructor.
     */
    public function __construct(TranslatorInterface $translator, CoreService $coreService)
    {
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
            ->add('file', TourFileType::class, [
                'label' => 'file',
                'required' => false,
                'placeholder_text' => $options['data']->getFile() ? $options['data']->getFile()->getOriginalName() : $this->translator->trans('no.file.selected'),
            ])
        ;

        if (!$options['data']->getEntries()->isEmpty()) {
            $builder->add('previewEntry', EntityType::class, [
                'label' => 'previewEntry',
                'required' => false,
                'class' => 'App:Entry',
                'choices' => $options['data']->getEntries(),
                'placeholder' => '',
                'attr' => [
                    'class' => 'select2 form-control',
                ],
            ]);
        }

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            $form = $event->getForm();
            $object = $form->getData();

            $object->setDescription($this->coreService->purifyString($object->getDescription()));
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tour::class,
        ]);
    }
}
