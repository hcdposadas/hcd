<?php

namespace MesaEntradaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class AnexoExpedienteType extends AbstractType {
	/**
	 * {@inheritdoc}
	 */
	public function buildForm( FormBuilderInterface $builder, array $options ) {
		$builder
			->add( 'descripcion' )
			->add( 'anexoFile',
//				VichFileType::class,
				VichImageType::class,
				[
					'label'       => 'Archivo',
					'required'    => false,
					'constraints' => [
						new File( [
							'maxSize'          => '8M',
							'mimeTypes'        => [
								'image/*'
							],
							'mimeTypesMessage' => 'Solo se aceptan imágenes .jpg, .png, .jpeg',
						] )
					]
				] );
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions( OptionsResolver $resolver ) {
		$resolver->setDefaults( array(
			'data_class' => 'MesaEntradaBundle\Entity\AnexoExpediente'
		) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix() {
		return 'mesaentradabundle_anexoexpediente';
	}


}
