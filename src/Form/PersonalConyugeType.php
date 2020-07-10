<?php

namespace App\Form;

use App\Entity\Persona;
use App\Entity\PersonalConyuge;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

class PersonalConyugeType extends AbstractType {
	public function buildForm( FormBuilderInterface $builder, array $options ) {
		$builder
			->add( 'estado',
				ChoiceType::class,
				[
					'label'   => 'Estado Civil',
					'choices' => [
						PersonalConyuge::ESTADO_CIVIL_SOLTERX     => PersonalConyuge::ESTADO_CIVIL_SOLTERX,
						PersonalConyuge::ESTADO_CIVIL_CASADX      => PersonalConyuge::ESTADO_CIVIL_CASADX,
						PersonalConyuge::ESTADO_CIVIL_DIVORCIADX  => PersonalConyuge::ESTADO_CIVIL_DIVORCIADX,
						PersonalConyuge::ESTADO_CIVIL_SEPARADX    => PersonalConyuge::ESTADO_CIVIL_SEPARADX,
						PersonalConyuge::ESTADO_CIVIL_VIUDX       => PersonalConyuge::ESTADO_CIVIL_VIUDX,
						PersonalConyuge::ESTADO_CIVIL_CONVIVIENTE => PersonalConyuge::ESTADO_CIVIL_CONVIVIENTE,
					],
				] )
			->add( 'fechaEnlace',
				DateType::class,
				[
					'label'    => 'Fecha de Enlace',
					'widget'   => 'single_text',
					'html5'    => true,
					'required' => false
				] )
			->add( 'lugarEnlace' )
			->add( 'trabaja' )
			->add( 'razonSocialLugarTrabajo' )
			->add( 'percibeAsignacionFamiliar' )
			->add( 'observaciones' )
			->add( 'persona',
				Select2EntityType::class,
				[
					'remote_route' => 'get_persona_por_dni',
					'class'        => Persona::class,
					'required'     => true,
					'placeholder'  => 'Por DNI'

				] );
	}

	public function configureOptions( OptionsResolver $resolver ) {
		$resolver->setDefaults( [
			'data_class' => PersonalConyuge::class,
		] );
	}
}
