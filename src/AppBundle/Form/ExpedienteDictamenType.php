<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ExpedienteDictamenType extends AbstractType {
	public function buildForm( FormBuilderInterface $builder, array $options ) {
		$builder->add( 'expediente' )
		        ->add( 'iniciadorComision' );
	}

	public function configureOptions( OptionsResolver $resolver ) {
		$resolver->setDefaults( array(
			'data_class' => 'MesaEntradaBundle\Entity\Expediente'
		) );
	}

	public function getBlockPrefix() {
		return 'app_bundle_expediente_dictamen_type';
	}
}
