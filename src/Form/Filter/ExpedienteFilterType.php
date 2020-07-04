<?php

namespace App\Form\Filter;

use App\Entity\Dependencia;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Form\Type\JqueryAutocompleteType;

class ExpedienteFilterType extends AbstractType {
	/**
	 * {@inheritdoc}
	 */
	public function buildForm( FormBuilderInterface $builder, array $options ) {
		$builder
//			->add( 'tipoExpediente',
//				EntityType::class,
//				[
//					'class'        => 'App\Entity\TipoExpediente',
//					'choice_label' => 'nombre',
//					'required'     => false,
//					'empty_data'   => ''
//				] )
			->add( 'textoDefinitivo' )
			->add( 'texto' )
			->add( 'extracto' )
			->add( 'expediente',
				null,
				[
					'label' => 'N° expediente'
				] )
			->add( 'anio',
				null,
				[
					'label' => 'Año'
				] )
			->add( 'letra' )
			->add( 'fecha',
				DateType::class,
				array(
					'widget' => 'single_text',
					'html5'  => true
				) )
			->add( 'registroMunicipal' )
			->add( 'iniciador' )
			->add( 'iniciadorParticular' )
			->add( 'dependencia')
			->add( 'buscar',
				SubmitType::class,
				array(
					'attr' => array( 'class' => 'btn btn-primary' ),
				) )
			->add( 'limpiar',
				ResetType::class,
				array(
					'attr' => array( 'class' => 'btn btn-default reset' ),
				) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions( OptionsResolver $resolver ) {
		$resolver->setDefaults( array(
//			'data_class' => 'App\Entity\Expediente'
			'csrf_protection' => false,
			'required'        => false
		) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix() {
		return 'App_expediente_filter';
	}


}
