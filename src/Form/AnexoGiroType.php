<?php

namespace App\Form;

use App\Entity\AnexoGiro;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class AnexoGiroType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
        ->add( 'anexoFile',
        //				VichFileType::class,
                        VichImageType::class,
                        [
                            'label'       => 'Archivo',
                            'required'    => false,
                            'constraints' => [
                                new File( [
                                    'maxSize'          => '10M',
                                    'mimeTypes'        => [
                                        'image/*',
                                        'application/pdf'
                                    ],
                                    'mimeTypesMessage' => 'Solo se aceptan imágenes .jpg, .png, .jpeg, .pdf',
                                ] )
                            ]
                        ] )
            ->add('titulo',TextType::class,['label'       => 'Descripción'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => AnexoGiro::class,
        ]);
    }
}
