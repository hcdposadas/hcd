<?php

namespace App\Form;

use App\Entity\ProyectoFirmado;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Vich\UploaderBundle\Form\Type\VichFileType;

class ProyectoFirmadoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
	        ->add( 'archivoFile',
		        VichFileType::class,
		        [
			        'label'       => 'Proyecto',
			        'required'    => true,
			        'constraints' => [
				        new File( [
					        'maxSize'          => '8M',
					        'mimeTypes'        => [
						        'application/pdf'
					        ],
					        'mimeTypesMessage' => 'Solo se archivos .pdf',
				        ] )
			        ]
		        ] );
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => ProyectoFirmado::class,
        ]);
    }
}
