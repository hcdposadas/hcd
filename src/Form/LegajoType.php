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
			->add( 'fechaIngreso',
				DateType::class,
				[
					'widget' => 'single_text',
					'html5'  => true
				] )
			->add( 'numero',
				TextType::class,
				[
					'label' => 'Nro Legajo'
				] )
			->add( 'numeroTarjeta' )
			->add( 'observaciones' )
//			->add( 'personalDeclaracionJuradas',
//				BootstrapCollectionType::class,
//				[
//					'entry_type'    => PersonalDeclaracionJuradaType::class,
//					'allow_add'     => true,
//					'max_items_add' => 1
//				] )
		;
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
		return 'app_legajo';
	}


}
