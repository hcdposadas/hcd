<?php

namespace AppBundle\Form\Filter;

use AppBundle\Entity\TipoOrdenPago;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrdenDePagoFilterType extends AbstractType {
	/**
	 * {@inheritdoc}
	 */
	public function buildForm( FormBuilderInterface $builder, array $options ) {
		$builder->add( 'numero' )
		        ->add( 'fechaRendicion' )
		        ->add( 'folios' )
		        ->add( 'numeroEstante' )
		        ->add( 'numeroCaja' )
		        ->add( 'paginaInicio' )
		        ->add( 'paginaFin' )
		        ->add( 'activo' )
		        ->add( 'tipoOrdenPago',
			        EntityType::class,
			        [
				        'class'    => TipoOrdenPago::class,
				        'required' => false
			        ] )
		        ->add( 'decreto' )
		        ->add( 'buscar',
			        SubmitType::class,
			        [
				        'attr' => array( 'class' => 'btn btn-primary' ),
			        ] )
		        ->add( 'limpiar',
			        ResetType::class,
			        [
				        'attr' => array( 'class' => 'btn btn-default reset' ),
			        ] );
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions( OptionsResolver $resolver ) {
		$resolver->setDefaults( array(
			'required' => false
		) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix() {
		return 'appbundle_ordendepago';
	}


}
