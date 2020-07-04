<?php

namespace App\Form\Filter;

use App\Entity\TipoProyecto;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProyectoFilterType extends AbstractType {
	public function buildForm( FormBuilderInterface $builder, array $options ) {
		$builder
			->add( 'tipoProyecto',
				EntityType::class,
				[
					'class'       => TipoProyecto::class,
					'required'    => true,
					'placeholder' => 'Seleccionar',

				] )
			->add('texto')
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

	public function configureOptions( OptionsResolver $resolver ) {
		$resolver->setDefaults( array(
			'required' => false
		) );
	}

	public function getBlockPrefix() {
		return 'mesa_entrada_bundle_proyecto_filter_type';
	}
}
