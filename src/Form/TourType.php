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
        /** @var Tour $tour */
        $tour = $options['data'];

        $builder
            ->add('name')
            ->add('description', TextareaType::class, [
                'required' => false,
                'attr' => [
                    'class' => 'wysiwyg',
                ],
            ])
            ->add('file', TourFileType::class, [
                'required' => false,
                'placeholder_text' => $tour->getFile() ? $tour->getFile()->getOriginalName() : $this->translator->trans('label.no_file_selected'),
            ])
        ;

        if (!$tour->getEntries()->isEmpty()) {
            $builder->add('previewEntry', EntityType::class, [
                'required' => false,
                'class' => 'App:Entry',
                'choices' => $tour->getEntries(),
                'placeholder' => '',
                'attr' => [
                    'class' => 'select2 form-control',
                ],
            ]);
        }

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            /** @var Tour $tour */
            $tour = $event->getForm()->getData();

            $tour->setDescription($this->coreService->purifyString($tour->getDescription()));
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tour::class,
        ]);
    }
}
