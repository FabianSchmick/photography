<?php

namespace AppBundle\Form;

use AppBundle\Entity\Author;
use AppBundle\Entity\Entry;
use AppBundle\Entity\Location;
use AppBundle\Entity\Tag;
use Doctrine\ORM;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EntryType extends AbstractType
{
    /**
     * @var ORM\EntityManager
     */
    protected $em;

    public function __construct(ORM\EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', TextType::class, array(
                'label' => false,
            ))
            ->add('description', TextareaType::class, array(
                'label' => false,
                'required' => false,
            ))
            ->add('author', EntityType::class, array(
                'label' => false,
                'required' => false,
                'class' => 'AppBundle:Author',
                'placeholder' => '',
            ))
            ->add('image', EntryImageType::class, array(
                'label' => false,
                'required' => false,
            ))
            ->add('location', EntityType::class, array(
                'label' => false,
                'required' => false,
                'class' => 'AppBundle:Location',
                'placeholder' => '',
            ))
            ->add('timestamp', DateType::class, array(
                'label' => false,
                'required' => false,
                'widget' => 'single_text',
                'format' => 'yyyy-MM-dd',
            ))
            ->add('tags', EntityType::class, array(
                'label' => false,
                'class' => 'AppBundle:Tag',
                'multiple' => true,
            ))
        ;

        $builder->addEventListener(FormEvents::PRE_SUBMIT, function (FormEvent $event) {
            $data = $event->getData();

            if (!empty($data['author'] = trim($data['author']))) {
                $data['author'] = $this->saveNewChoiceByName($data['author'], Author::class, 'AppBundle:Author');
            }

            if (!empty($data['location'] = trim($data['location']))) {
                $data['location'] = $this->saveNewChoiceByName($data['location'], Location::class, 'AppBundle:Location');
            }

            foreach ($data['tags'] as $key => $tag) {
                if (!empty($tag = trim($tag))) {
                    $data['tags'][$key] = $this->saveNewChoiceByName($tag, Tag::class, 'AppBundle:Tag');
                }
            }

            $event->setData($data);
        });
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Entry::class,
        ]);
    }

    protected function saveNewChoiceByName($choice, $class, $repo)
    {
        $entity = $this->em->getRepository($repo)->find($choice);

        if (!$entity) {
            $entity = new $class();
            $entity->setName($choice);
            $this->em->persist($entity);
            $this->em->flush();
        }

        return $entity->getId();
    }
}
