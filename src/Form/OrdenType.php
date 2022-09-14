<?php

namespace App\Form;

use App\Entity\Orden;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrdenType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('medico')
            ->add('diagnostico')
            ->add('fecha')
            ->add('paciente')
            ->add('articulo')
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Orden::class,
        ]);
    }
}
