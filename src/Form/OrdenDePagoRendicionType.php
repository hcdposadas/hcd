<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Vich\UploaderBundle\Form\Type\VichFileType;

class OrdenDePagoRendicionType extends AbstractType {
	/**
	 * {@inheritdoc}
	 */
	public function buildForm( FormBuilderInterface $builder, array $options ) {
		$builder
			->add( 'fecha',
				DateType::class,
				[
					'widget' => 'single_text',
					'html5'  => true
				] )
			->add( 'ordenDePagoFile',
				VichFileType::class,
				[
					'label'       => 'Archivo',
					'required'    => false,
					'constraints' => [
						new File( [
							'maxSize'          => '75M',
							'mimeTypes'        => [
								'image/*',
								'application/pdf'
							],
							'mimeTypesMessage' => 'Solo se aceptan .jpg, .png, .jpeg, .pdf',
						] )
					]
				] );
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions( OptionsResolver $resolver ) {
		$resolver
			->setDefaults( array(
				'data_class' => 'App\Entity\OrdenDePagoRendicion'
			) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix() {
		return 'App_ordendepagorendicion';
	}


}
