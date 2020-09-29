<?php

namespace Extrablind\MonitHomeBundle\MonitHomeModules\Activators\RelayActivator\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class BinaryType extends AbstractType
{
    public $state;

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('state', CheckboxType::class,
                [
                    'mapped' => false,
                    'attr'   => [
                        'data-toggle' => 'toggle',
                        'autofocus'   => true,
                    ],
                    'required' => false,
                    'label'    => 'state',
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => null,
        ]);
    }
}
