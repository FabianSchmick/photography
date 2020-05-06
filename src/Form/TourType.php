<?php

namespace App\Form;

use App\Entity\Tour;
use App\Service\CoreService;
use App\Service\TourService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TourType extends AbstractType
{
    /**
     * @var CoreService
     */
    private $coreService;

    /**
     * @var TourService
     */
    private $tourService;

    /**
     * TourType constructor.
     */
    public function __construct(CoreService $coreService, TourService $tourService)
    {
        $this->coreService = $coreService;
        $this->tourService = $tourService;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Tour $tour */
        $tour = $options['data'];

        $placeholderTour = clone $tour;
        if ($tour->getFile()) {
            $this->tourService->setGpxData($placeholderTour);
        }

        $builder
            ->add('name')
            ->add('description', TextareaType::class, [
                'required' => false,
            ])
            ->add('distance', NumberType::class, [
                'required' => false,
                'unit' => 'unit.distance',
                'attr' => [
                    'placeholder' => $placeholderTour->getDistance() ? number_format($placeholderTour->getDistance(), 1, ',', '.') : null,
                ],
            ])
            ->add('duration', TimeType::class, [
                'required' => false,
                'unit' => 'unit.duration',
                'html5' => false,
                'widget' => 'single_text',
                'attr' => [
                    'placeholder' => $this->tourService->formatDuration($placeholderTour->getDuration()),
                ],
            ])
            ->add('maxAltitude', NumberType::class, [
                'required' => false,
                'unit' => 'unit.altitude',
                'attr' => [
                    'placeholder' => $placeholderTour->getMaxAltitude(),
                ],
            ])
            ->add('minAltitude', NumberType::class, [
                'required' => false,
                'unit' => 'unit.altitude',
                'attr' => [
                    'placeholder' => $placeholderTour->getMinAltitude(),
                ],
            ])
            ->add('cumulativeElevationGain', NumberType::class, [
                'required' => false,
                'unit' => 'unit.cumulativeElevationGain',
                'attr' => [
                    'placeholder' => $placeholderTour->getCumulativeElevationGain(),
                ],
            ])
            ->add('file', TourFileType::class, [
                'required' => false,
                'placeholder_text' => $tour->getFile() ? $tour->getFile()->getOriginalName() : 'label.no_file_selected',
            ])
        ;

        if (!$tour->getEntries()->isEmpty()) {
            $builder->add('previewEntry', EntityType::class, [
                'required' => false,
                'class' => 'App:Entry',
                'choices' => $tour->getEntries(),
                'placeholder' => '',
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
