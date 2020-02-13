<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class OrdenDePagoType extends AbstractType {
	/**
	 * {@inheritdoc}
	 */
	public function buildForm( FormBuilderInterface $builder, array $options ) {
		$builder
			->add( 'tipoOrdenPago' )
			->add( 'numero' )
//		        ->add( 'fechaRendicion',
//			        DateType::class,
//			        [
//				        'widget' => 'single_text',
//				        'html5'  => true,
//				        'label'=> 'Fecha de Rendición'
//			        ] )
			->add( 'folios' )
			->add( 'paginaInicio' )
			->add( 'paginaFin' )
			->add( 'tipoOrdenPago' )
			->add( 'numeroEstante' )
			->add( 'numeroCaja' )
			->add( 'decreto' )
			->add( 'ordenDePagoFile',
				VichFileType::class,
				[
					'label'       => 'Archivo',
					'required'    => false,
					'constraints' => [
						new File( [
//						        'maxSize'          => '8M',
//						        'mimeTypes'        => [
//							        'image/*'
//						        ],
//						        'mimeTypesMessage' => 'Solo se aceptan imágenes .jpg, .png, .jpeg',
						] )
					]
				] )
			->add( 'observacion' )
			->add( 'activo' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions( OptionsResolver $resolver ) {
		$resolver->setDefaults( array(
			'data_class' => 'AppBundle\Entity\OrdenDePago'
		) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix() {
		return 'appbundle_ordendepago';
	}


}
