<?php

namespace App\Form;

use App\Entity\Entry;
use App\Entity\Tag;
use App\Form\Custom\PurifyTextareaType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TagType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        /** @var Tag $tag */
        $tag = $options['data'];

        $builder
            ->add('name')
            ->add('description', PurifyTextareaType::class, [
                'required' => false,
            ])
            ->add('sort', NumberType::class, [
                'required' => false,
            ])
        ;

        if (!$tag->getEntries()->isEmpty()) {
            $builder->add('previewEntry', EntityType::class, [
                'required' => false,
                'class' => Entry::class,
                'choices' => $tag->getEntries(),
                'placeholder' => '',
            ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Tag::class,
        ]);
    }
}
