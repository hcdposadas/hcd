<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AltaExpedienteDictamenType extends AbstractType {
	public function buildForm( FormBuilderInterface $builder, array $options ) {

		$builder
			->add( 'tipoProyecto',
				null,
				[
					'required'    => true,
					'placeholder' => 'Seleccionar',
					'attr'        => [ 'class' => 'tipo-proyecto select2' ]
				] )
			->add( 'expediente',
				TextType::class,
				[
					'required' => true
				] )
			->add( 'letra',
				TextType::class,
				[
					'required' => true
				] )
			->add( 'periodoLegislativo',
				null,
				[
					'attr' => [ 'class' => 'select2' ]
				] );

	}

	public function configureOptions( OptionsResolver $resolver ) {
		$resolver->setDefaults( array(
			'data_class' => 'MesaEntradaBundle\Entity\Expediente'
		) );
	}

	public function getBlockPrefix() {
		return 'app_bundle_alta_expediente_dictamen_type';
	}
}
