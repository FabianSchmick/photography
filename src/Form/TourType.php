<?php

namespace App\Form;

use App\Entity\Location;
use App\Entity\Tour;
use App\Entity\TourCategory;
use App\Form\DataTransformer\DateIntervalTransformer;
use App\Service\CoreService;
use App\Service\TourService;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
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
     * @var DateIntervalTransformer
     */
    private $transformer;

    /**
     * TourType constructor.
     */
    public function __construct(CoreService $coreService, TourService $tourService, DateIntervalTransformer $transformer)
    {
        $this->coreService = $coreService;
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
            ->add('description', TextareaType::class, [
                'required' => false,
            ])
            ->add('directions', TextareaType::class, [
                'required' => false,
            ])
            ->add('equipmentAndSafety', TextareaType::class, [
                'required' => false,
            ])
            ->add('tourCategory', EntityType::class, [
                'required' => false,
                'class' => 'App:TourCategory',
                'placeholder' => '',
                'query_builder' => function (EntityRepository $er): QueryBuilder {
                    return $er->createQueryBuilder('c')
                        ->orderBy('c.name', 'ASC');
                },
                'attr' => [
                    'class' => 'select2-add',
                ],
            ])
            ->add('locations', EntityType::class, [
                'required' => false,
                'multiple' => true,
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
            ->add('file', TourFileType::class, [
                'required' => false,
                'placeholder_text' => $tour->getFile() ? $tour->getFile()->getOriginalName() : 'label.no_file_selected',
            ])
            ->add('sort', NumberType::class, [
                'required' => false,
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

        $builder->get('duration')->addModelTransformer($this->transformer);

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();

            if (!empty($data['tourCategory'] = trim($data['tourCategory']))) {
                $data['tourCategory'] = $this->coreService->saveNewEntityByName($data['tourCategory'], TourCategory::class, 'App:TourCategory');
            }

            foreach ($data['locations'] as $key => $location) {
                if (!empty($location = trim($location))) {
                    $data['locations'][$key] = $this->coreService->saveNewEntityByName($location, Location::class, 'App:Location');
                }
            }

            $event->setData($data);
        });

        $builder->addEventListener(FormEvents::SUBMIT, function (FormEvent $event) {
            /** @var Tour $tour */
            $tour = $event->getForm()->getData();

            $tour->setDescription($this->coreService->purifyString($tour->getDescription()));
            $tour->setDirections($this->coreService->purifyString($tour->getDirections()));
            $tour->setEquipmentAndSafety($this->coreService->purifyString($tour->getEquipmentAndSafety()));
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tour::class,
        ]);
    }
}
