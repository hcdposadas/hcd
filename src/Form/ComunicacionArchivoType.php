<?php

namespace App\Form;

use App\Entity\ComunicacionArchivo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class ComunicacionArchivoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('archivoFile', VichFileType::class, [
                'label' => 'Archivo',
                'required' => true,
                'allow_delete' => true,
                'download_uri' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ComunicacionArchivo::class,
        ]);
    }
}
