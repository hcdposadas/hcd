<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GiroType extends AbstractType {
	/**
	 * {@inheritdoc}
	 */
	public function buildForm( FormBuilderInterface $builder, array $options ) {
		$builder
			->add( 'comisionDestino',
				null,
				[
					'attr' => [ 'class' => 'select2' ]
				] )
			->add('texto')
			->add( 'fechaGiro',
				DateType::class,
                array(
                    'widget' => 'single_text',
                    'html5'  => true
                ) )
			->add('orden')
			->add( 'cabecera',
				null,
				[
					'attr' => [ 'class' => 'comision_cabecera' ]
				] );
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions( OptionsResolver $resolver ) {
		$resolver->setDefaults( array(
			'data_class' => 'App\Entity\Giro'
		) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix() {
		return 'App_giro';
	}


}
