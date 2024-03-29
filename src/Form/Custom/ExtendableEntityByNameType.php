<?php

namespace App\Form\Custom;

use App\Service\CoreService;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExtendableEntityByNameType extends AbstractType
{
    public function __construct(private readonly CoreService $coreService)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) use ($options) {
            $data = $event->getData();

            // Configured as multiple
            if (is_array($data)) {
                foreach ($data as $key => $choice) {
                    $data[$key] = $this->coreService->createNewEntityByName($options['class'], $choice);
                }
            } else {
                $data = $this->coreService->createNewEntityByName($options['class'], $data);
                $data = strval($data); // Symfony expects a string here for the entity choice
            }

            $event->setData($data);
        });
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'placeholder' => '',
            'query_builder' => fn (EntityRepository $er): QueryBuilder => $er->createQueryBuilder('e')
                ->orderBy('e.name', 'ASC'),
        ]);
    }

    public function getParent(): string
    {
        return EntityType::class;
    }
}
