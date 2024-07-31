<?php

namespace App\Form;

use App\Entity\InformeDem;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichImageType;
use Symfony\Component\Validator\Constraints\File;

class InformeDemType extends AbstractType
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
                                    'maxSize'          => '50M',
                                    'mimeTypes'        => [
                                        'image/*',
                                        'application/pdf'
                                    ],
                                    'mimeTypesMessage' => 'Solo se aceptan imágenes .jpg, .png, .jpeg, .pdf',
                                ] )
                            ]
                        ] );
            }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => InformeDem::class,
        ]);
    }
}
