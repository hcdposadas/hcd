<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CargarDictamenType extends AbstractType {
	public function buildForm( FormBuilderInterface $builder, array $options ) {

	}

	public function configureOptions( OptionsResolver $resolver ) {

	}

	public function getBlockPrefix() {
		return 'app_bundle_cargar_dictamen_type';
	}
}
