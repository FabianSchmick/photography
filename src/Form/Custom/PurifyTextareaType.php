<?php

namespace App\Form\Custom;

use App\Service\CoreService;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;

class PurifyTextareaType extends AbstractType
{
    public function __construct(private readonly CoreService $coreService)
    {
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();

            $event->setData($this->coreService->purifyString($data));
        });
    }

    public function getParent(): string
    {
        return TextareaType::class;
    }
}
