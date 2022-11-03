<?php

namespace App\Form;

use App\Config\TourDurationFormula;
use App\Entity\TourCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TourCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $types = array_map(fn ($enum) => $enum->name, TourDurationFormula::cases());

        $builder
            ->add('name')
            ->add('formulaType', ChoiceType::class, [
                'required' => false,
                'placeholder' => '',
                'choices' => array_combine($types, $types),
                'choice_label' => fn ($choice, $key) => "label.$key",
            ])
            ->add('hasLevelOfDifficulty', CheckboxType::class, [
                'required' => false,
            ])
            ->add('sort', NumberType::class, [
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TourCategory::class,
        ]);
    }
}
