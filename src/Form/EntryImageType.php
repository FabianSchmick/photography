<?php

namespace App\Form;

use App\Entity\EntryImage;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Contracts\Translation\TranslatorInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;

class EntryImageType extends AbstractType
{
    final public const ALLOWED_MIME_TYPES = [
        'image/png',
        'image/jpeg',
        'image/gif',
    ];

    public function __construct(private readonly TranslatorInterface $translator)
    {
    }

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
                        'mimeTypes' => self::ALLOWED_MIME_TYPES,
                    ]),
                    new NotBlank(),
                ],
                'attr' => [
                    'placeholder' => $this->translator->trans($options['placeholder_text']),
                    'accept' => implode(',', self::ALLOWED_MIME_TYPES),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => EntryImage::class,
            'placeholder_text' => '',
        ]);
    }
}
