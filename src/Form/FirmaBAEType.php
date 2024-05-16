<?php

namespace App\Form;

use App\Entity\ProyectoBAE;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;


class FirmaBaeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {

        $builder
        ->add( 'firmadoFile',
        VichFileType::class,
        [
            'label'        => 'Giro Firmado',
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