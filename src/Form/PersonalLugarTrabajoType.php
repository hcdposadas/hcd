<?php

namespace App\Form;

use App\Entity\PersonalLugarTrabajo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonalLugarTrabajoType extends AbstractType {
	public function buildForm( FormBuilderInterface $builder, array $options ) {
		$builder
			->add( 'fechaDesde',
				DateType::class,
				[

					'widget' => 'single_text',
					'html5'  => true
				] )
			->add( 'fechaHasta',
				DateType::class,
				[

					'widget'   => 'single_text',
					'html5'    => true,
					'required' => false
				] )
			->add( 'actual' )
			->add( 'areaAdministrativa' );
	}

	public function configureOptions( OptionsResolver $resolver ) {
		$resolver->setDefaults( [
			'data_class' => PersonalLugarTrabajo::class,
		] );
	}
}
