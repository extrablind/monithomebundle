<?php

namespace Extrablind\MonitHomeBundle\Form;

use Extrablind\MonitHomeBundle\Entity\Hardware;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class HardwareType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
    ->add('name',
    \Symfony\Component\Form\Extension\Core\Type\TextType::class,
    [
        'attr' => [
            //        'readonly' => true,
            'autofocus' => true, ],
        'label' => 'label.name',
    ])
      ->add('description',
      \Symfony\Component\Form\Extension\Core\Type\TextareaType::class,
      [
          'label' => 'label.description',
      ])
      ->add('connector',
      \Symfony\Component\Form\Extension\Core\Type\TextType::class,
      [
          'required' => false,
          'attr'     => [
              'readonly' => false,
          ],
          'label' => 'label.connector',
      ])
      ->add('identifier',
      \Symfony\Component\Form\Extension\Core\Type\TextType::class,
      [
          'label' => 'label.identifier',
      ])
      ->add('active',
      \Symfony\Component\Form\Extension\Core\Type\CheckboxType::class,
      [
          'label'    => 'label.activation',
          'required' => false,
      ])
      ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Hardware::class,
        ]);
    }
}
