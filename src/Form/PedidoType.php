<?php

namespace App\Form;

use App\Entity\ProyectoBAE;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;


class PedidoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add( 'pedidoFile',
        VichFileType::class,
        [
            'label'        => 'Pedido de informe firmado',
            'required'     => true,
            'allow_delete' => true, // optional, default is true
            'download_uri' => true, // optional, default is true
        ] )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProyectoBAE::class,
        ]);
    }
}
