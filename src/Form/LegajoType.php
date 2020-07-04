<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class LegajoType extends AbstractType {
	/**
	 * {@inheritdoc}
	 */
	public function buildForm( FormBuilderInterface $builder, array $options ) {
		$builder
			->add( 'tratamiento',
				TextType::class,
				[
					'attr' => [ 'placeholder' => 'Sr./Dr./Ing.' ]
				] )
			->add( 'profesion' )
			->add( 'fechaIngreso',
				DateType::class,
				array(
					'widget' => 'single_text',
					'format' => 'dd/MM/yyyy',
					'attr'   => array(
						'class' => 'datepicker',
					),
				) )
			->add( 'numero',
				TextType::class,
				[
					'label' => 'Nro Legajo'
				] )
			->add( 'numeroTarjeta' )
			->add( 'situacionRevista',
				ChoiceType::class,
				[
					'choices' => array(
						'Planta Permanente' => 'Planta Permanente',
						'Contratado'        => 'Contratado'
					),
				] )
			->add( 'observaciones' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions( OptionsResolver $resolver ) {
		$resolver->setDefaults( array(
			'data_class' => 'App\Entity\Legajo'
		) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix() {
		return 'App_legajo';
	}


}
