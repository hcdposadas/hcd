<?php

namespace App\Form;

use App\Entity\ExpedienteBloqueado;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;


class BloqueadoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('numero')
            ->add('letra',TextType::class,['required' => true,'attr' => array( 'class' => 'text-uppercase' )])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ExpedienteBloqueado::class,
        ]);
    }
}
