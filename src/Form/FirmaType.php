<?php

namespace App\Form;

use App\Entity\Expediente;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;


class FirmaType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add( 'expedienteInternoFile',
        VichFileType::class,
        [
            'label'        => 'Proyecto Firmado',
            'required'     => true,
            'allow_delete' => true, // optional, default is true
            'download_uri' => true, // optional, default is true
        ] )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Expediente::class,
        ]);
    }
}
