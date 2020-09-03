<?php

namespace App\Form;

use App\Entity\PersonalDeclaracionJurada;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonalDeclaracionJuradaType extends AbstractType {
	public function buildForm( FormBuilderInterface $builder, array $options ) {
		$builder
			->add( 'anio' )
			->add( 'tratamiento',
				TextType::class,
				[
					'attr' => [ 'placeholder' => 'Sr./Dr./Ing.' ]
				] )
			->add( 'situacionRevista',
				ChoiceType::class,
				[
					'choices' => [
						'Planta Permanente' => 'Planta Permanente',
						'Contratado'        => 'Contratado'
					],
				] )
			->add( 'profesion',
				TextType::class,
				[
					'label' => 'Profesión'
				] )
			->add( 'nivelEstudios' )
			->add( 'titulo' )
			->add( 'aniosCursados' )
			->add( 'fechaPresentacion' );
	}

	public function configureOptions( OptionsResolver $resolver ) {
		$resolver->setDefaults( [
			'data_class' => PersonalDeclaracionJurada::class,
		] );
	}
}
