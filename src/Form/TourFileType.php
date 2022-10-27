<?php

namespace App\Form;

use App\Entity\TourFile;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Contracts\Translation\TranslatorInterface;
use Vich\UploaderBundle\Form\Type\VichImageType;

class TourFileType extends AbstractType
{
    const ALLOWED_MIME_TYPES = [
        'text/xml',
        'application/gpx+xml',
    ];

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * TourType constructor.
     */
    public function __construct(TranslatorInterface $translator)
    {
        $this->translator = $translator;
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
                ],
                'attr' => [
                    'placeholder' => $this->translator->trans($options['placeholder_text']),
                    'accept' => '.gpx,'.implode(',', self::ALLOWED_MIME_TYPES),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => TourFile::class,
            'placeholder_text' => '',
        ]);
    }
}
