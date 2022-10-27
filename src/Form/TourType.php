<?php

namespace App\Form;

use App\Entity\Entry;
use App\Entity\Location;
use App\Entity\Tour;
use App\Entity\TourCategory;
use App\Form\Custom\ExtendableEntityByNameType;
use App\Form\Custom\PurifyTextareaType;
use App\Form\DataTransformer\DateIntervalTransformer;
use App\Service\TourService;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TourType extends AbstractType
{
    /**
     * @var TourService
     */
    private $tourService;

    /**
     * @var DateIntervalTransformer
     */
    private $transformer;

    /**
     * TourType constructor.
     */
    public function __construct(TourService $tourService, DateIntervalTransformer $transformer)
    {
        $this->tourService = $tourService;
        $this->transformer = $transformer;
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
            ->add('file', TourFileType::class, [
                'required' => !$tour->getFile(),
                'placeholder_text' => $tour->getFile() ? $tour->getFile()->getOriginalName() : 'label.no_file_selected',
            ])
            ->add('description', PurifyTextareaType::class, [
                'required' => false,
            ])
            ->add('directions', PurifyTextareaType::class, [
                'required' => false,
            ])
            ->add('equipmentAndSafety', PurifyTextareaType::class, [
                'required' => false,
            ])
            ->add('tourCategory', ExtendableEntityByNameType::class, [
                'required' => false,
                'class' => TourCategory::class,
            ])
            ->add('locations', ExtendableEntityByNameType::class, [
                'required' => false,
                'multiple' => true,
                'class' => Location::class,
            ])
            ->add('distance', NumberType::class, [
                'required' => false,
                'unit' => 'unit.distance',
                'attr' => [
                    'placeholder' => $placeholderTour->getDistance() ? number_format($placeholderTour->getDistance(), 1, ',', '.') : null,
                ],
            ])
            ->add('duration', TextType::class, [
                'required' => false,
                'unit' => 'unit.duration',
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
            ->add('cumulativeElevationLoss', NumberType::class, [
                'required' => false,
                'unit' => 'unit.cumulativeElevationLoss',
                'attr' => [
                    'placeholder' => $placeholderTour->getCumulativeElevationLoss(),
                ],
            ])
        ;

        if ($tour->getTourCategory() && $tour->getTourCategory()->isHasLevelOfDifficulty()) {
            $builder->add('levelOfDifficulty', ChoiceType::class, [
                'required' => false,
                'choices' => Tour::LEVEL_OF_DIFFICULTY,
                'placeholder' => '',
            ]);
        }

        if (!$tour->getEntries()->isEmpty()) {
            $builder->add('previewEntry', EntityType::class, [
                'required' => false,
                'class' => Entry::class,
                'choices' => $tour->getEntries(),
                'placeholder' => '',
            ]);
        }

        $builder->add('sort', NumberType::class, [
            'required' => false,
        ]);

        $builder->get('duration')->addModelTransformer($this->transformer);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tour::class,
        ]);
    }
}
