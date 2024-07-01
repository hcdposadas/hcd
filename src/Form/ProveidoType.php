<?php

namespace App\Form;

use App\Entity\Proveido;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;


class ProveidoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
/*             ->add('caratulaFile',
            VichFileType::class,
            [
                'label'        => 'Caratula',
                'required'     => false,
                'allow_delete' => true, // optional, default is true
                'download_uri' => true, // optional, default is true
            ] ) */
            ->add('archivoFile',
            VichFileType::class,
            [
                'label'        => 'Archivo Proveido',
                'required'     => false,
                'allow_delete' => true, // optional, default is true
                'download_uri' => true, // optional, default is true
            ] )
/*             ->add('cierreFile',
            VichFileType::class,
            [
                'label'        => 'Cierre ',
                'required'     => false,
                'allow_delete' => true, // optional, default is true
                'download_uri' => true, // optional, default is true
            ] ) */
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Proveido::class,
        ]);
    }
}
