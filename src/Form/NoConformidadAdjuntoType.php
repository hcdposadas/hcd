<?php

namespace App\Form;

use App\Entity\NoConformidadAdjunto;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class NoConformidadAdjuntoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('archivoFile', VichFileType::class, [
                'label' => 'Archivo',
                'required' => false,
                'allow_delete' => true,
                'delete_label' => '¿Eliminar archivo?',
                'download_uri' => false,
                'attr' => [
                    'accept' => 'image/*,.pdf,.doc,.docx,.txt'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => NoConformidadAdjunto::class,
        ]);
    }
} 