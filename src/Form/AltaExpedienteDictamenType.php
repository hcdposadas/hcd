<?php

namespace App\Form;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Valid;

class AltaExpedienteDictamenType extends AbstractType {
	public function buildForm( FormBuilderInterface $builder, array $options ) {

		$builder
			->add( 'tipoProyecto',
				null,
				[
					'required'    => false,
					'placeholder' => 'Seleccionar',
					'attr'        => [ 'class' => 'tipo-proyecto select2' ]
				] )
			->add( 'nota' )
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
				] )
		;

	}

	public function configureOptions( OptionsResolver $resolver ) {
		$resolver->setDefaults( array(
			'data_class' => 'App\Entity\Expediente',
			'constraints' => new Valid()
		) );
	}

	public function getBlockPrefix() {
		return 'app_bundle_alta_expediente_dictamen_type';
	}
}
