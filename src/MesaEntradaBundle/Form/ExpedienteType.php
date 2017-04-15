<?php

namespace MesaEntradaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExpedienteType extends AbstractType {
	/**
	 * {@inheritdoc}
	 */
	public function buildForm( FormBuilderInterface $builder, array $options ) {
		$builder
			->add( 'tipoExpediente' )
			->add( 'textoDefinitivo' )
			->add( 'extracto' )
			->add( 'expediente' )
			->add( 'anio' )
			->add( 'letra' )
			->add( 'fecha',
				DateType::class,
				array(
					'widget' => 'single_text',
					'format' => 'dd/MM/yyyy',
					'attr'   => array(
						'class'       => 'datepicker',
					),
				) )
			->add( 'registroMunicipal' )
			->add( 'expedienteInterno' )
			->add( 'expedienteExterno' )
			->add( 'iniciador' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions( OptionsResolver $resolver ) {
		$resolver->setDefaults( array(
			'data_class' => 'MesaEntradaBundle\Entity\Expediente'
		) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix() {
		return 'mesaentradabundle_expediente';
	}


}
