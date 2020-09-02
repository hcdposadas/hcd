<?php

namespace App\Form;

use App\Entity\PersonalAsistencia;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonalAsistenciaType extends AbstractType {
	public function buildForm( FormBuilderInterface $builder, array $options ) {
		$builder
			->add( 'fecha',
				DateType::class,
				[
					'widget' => 'single_text',
					'html5'  => true
				] )
			->add( 'horaEntrada',
				TimeType::class,
				[
					'widget' => 'single_text',
					'html5'  => true
				] )
			->add( 'horaSalida',
				TimeType::class,
				[
					'widget' => 'single_text',
					'html5'  => true
				] )
			;
	}

	public function configureOptions( OptionsResolver $resolver ) {
		$resolver->setDefaults( [
			'data_class' => PersonalAsistencia::class,
		] );
	}
}
