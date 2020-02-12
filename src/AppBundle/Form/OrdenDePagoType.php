<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OrdenDePagoType extends AbstractType {
	/**
	 * {@inheritdoc}
	 */
	public function buildForm( FormBuilderInterface $builder, array $options ) {
		$builder->add( 'numero' )
		        ->add( 'fechaRendicion',
			        DateType::class,
			        [
				        'widget' => 'single_text',
				        'html5'  => true
			        ] )
		        ->add( 'folios' )
		        ->add( 'ubicacion' )
		        ->add( 'paginaInicio' )
		        ->add( 'paginaFin' )
		        ->add( 'activo' )
		        ->add( 'tipoOrdenPago' )
		        ->add( 'decreto' );
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
