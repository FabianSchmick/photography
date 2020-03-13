<?php

namespace App\Form;

use App\Entity\EntryImage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Vich\UploaderBundle\Form\Type\VichImageType;

class EntryImageType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('file', VichImageType::class, [
                'label' => false,
                'data_class' => null,
                'error_bubbling' => true,
                'allow_delete' => false,
                'constraints' => [
                    new File([
                        'mimeTypes' => ['image/png', 'image/jpeg', 'image/gif'],
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EntryImage::class,
        ]);
    }
}
