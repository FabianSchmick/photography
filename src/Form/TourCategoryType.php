<?php

namespace App\Form;

use App\Entity\Tour;
use App\Entity\TourCategory;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TourCategoryType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $types = array_keys(Tour::FORMULA_DEFINITIONS);

        $builder
            ->add('name')
            ->add('formulaType', ChoiceType::class, [
                'required' => false,
                'placeholder' => '',
                'choices' => array_combine($types, $types),
                'choice_label' => function ($choice, $key) {
                    return "label.{$key}";
                },
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
