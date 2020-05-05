<?php

namespace AppBundle\Form;

use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use MesaEntradaBundle\Form\GiroType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use UtilBundle\Form\Type\BootstrapCollectionType;
use UtilBundle\Form\Type\Select2EntityType;
use Symfony\Component\Validator\Constraints\Valid;

class ProyectoBAEIncorporarType extends AbstractType {
	/**
	 * {@inheritdoc}
	 */
	public function buildForm( FormBuilderInterface $builder, array $options ) {
		$builder
			->add( 'boletinAsuntoEntrado',
				null,
				[
					'label'       => 'Sesión',
					'required'    => true,
					'placeholder' => 'Seleccionar'
				] )
			->add( 'extracto',
				CKEditorType::class,
				[
					'required' => true,
					'config'   => array(
						'uiColor' => '#ffffff',
					)
				] )
			->add( 'giros',
				BootstrapCollectionType::class,
				[
					'entry_type'   => GiroType::class,
					'allow_add'    => true,
					'allow_delete' => true,
					'by_reference' => false,
				] )
			->add( 'esInformeDem',
				null,
				[
					'label' => 'Informe DEM'
				] )
			->add( 'tratamientoSobretabla',
				null,
				[
					'label' => 'Tratamiento Sobretabla'
				] );
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions( OptionsResolver $resolver ) {
		$resolver->setDefaults( array(
			'data_class'  => 'AppBundle\Entity\ProyectoBAE',
			'constraints' => new Valid()
		) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix() {
		return 'appbundle_proyectobae_incorporar';
	}


}
