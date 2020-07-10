<?php

namespace App\Form;

use App\Entity\PersonalDeclaracionJurada;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonalDeclaracionJurada1Type extends AbstractType {
	public function buildForm( FormBuilderInterface $builder, array $options ) {
		$builder
			->add( 'anio',
				TextType::class,
				[
					'label' => 'Año'
				] )
			->add( 'tratamiento',
				TextType::class,
				[
					'attr'     => [ 'placeholder' => 'Sr./Dr./Ing.' ],
					'required' => false
				] )
			->add( 'situacionRevista',
				ChoiceType::class,
				[
					'choices' => [
						'Planta Permanente' => 'Planta Permanente',
						'Contratado'        => 'Contratado'
					],
				] )
			->add( 'categoria',
				TextType::class,
				[
					'label'    => 'Categoría',
					'required' => false
				] )
			->add( 'profesion',
				TextType::class,
				[
					'label'    => 'Profesión',
					'required' => false
				] )
			->add( 'nivelEstudios',
				ChoiceType::class,
				[
					'label'   => 'Nivel de estudios',
					'choices' => [
						PersonalDeclaracionJurada::NIVEL_ESTUDIOS_PRIMARIO_COMPLETO        => PersonalDeclaracionJurada::NIVEL_ESTUDIOS_PRIMARIO_COMPLETO,
						PersonalDeclaracionJurada::NIVEL_ESTUDIOS_PRIMARIO_INCOMPLETO      => PersonalDeclaracionJurada::NIVEL_ESTUDIOS_PRIMARIO_INCOMPLETO,
						PersonalDeclaracionJurada::NIVEL_ESTUDIOS_SECUNDARIO_COMPLETO      => PersonalDeclaracionJurada::NIVEL_ESTUDIOS_SECUNDARIO_COMPLETO,
						PersonalDeclaracionJurada::NIVEL_ESTUDIOS_SECUNDARIO_INCOMPLETO    => PersonalDeclaracionJurada::NIVEL_ESTUDIOS_SECUNDARIO_INCOMPLETO,
						PersonalDeclaracionJurada::NIVEL_ESTUDIOS_TERCIARIO_COMPLETO       => PersonalDeclaracionJurada::NIVEL_ESTUDIOS_TERCIARIO_COMPLETO,
						PersonalDeclaracionJurada::NIVEL_ESTUDIOS_TERCIARIO_INCOMPLETO     => PersonalDeclaracionJurada::NIVEL_ESTUDIOS_TERCIARIO_INCOMPLETO,
						PersonalDeclaracionJurada::NIVEL_ESTUDIOS_UNIVERSITARIO_COMPLETO   => PersonalDeclaracionJurada::NIVEL_ESTUDIOS_UNIVERSITARIO_COMPLETO,
						PersonalDeclaracionJurada::NIVEL_ESTUDIOS_UNIVERSITARIO_INCOMPLETO => PersonalDeclaracionJurada::NIVEL_ESTUDIOS_UNIVERSITARIO_INCOMPLETO,
					],
				] )
			->add( 'titulo',
				TextType::class,
				[
					'label'    => 'Título de',
					'required' => false
				] )
			->add( 'aniosCursados',
				IntegerType::class,
				[
					'label'    => 'Años Cursados',
					'required' => false
				] )
			->add( 'fechaPresentacion',
				DateType::class,
				[
					'label'  => 'Fecha de Presentación',
					'widget' => 'single_text',
					'html5'  => true
				] )
			->add( 'personalDDJJPersonaACargos',
				BootstrapCollectionType::class,
				[
					'entry_type'   => PersonalDDJJPersonaACargoType::class,
					'allow_add'    => true,
					'by_reference' => false,

				] )
			->add( 'personalDDJJConyuges',
				BootstrapCollectionType::class,
				[
					'entry_type'    => PersonalDDJJConyugeType::class,
					'allow_add'     => true,
					'max_items_add' => 1,
					'by_reference'  => false

				] )
			->add( 'lugarTrabajo', PersonalLugarTrabajoType::class );
	}

	public function configureOptions( OptionsResolver $resolver ) {
		$resolver->setDefaults( [
			'data_class'         => PersonalDeclaracionJurada::class
		] );
	}
}
