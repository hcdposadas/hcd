<?php

namespace App\Form;

use App\Entity\Persona;
use App\Entity\PersonalDeclaracionJurada;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

class PersonaACargoType extends AbstractType {
	/**
	 * {@inheritdoc}
	 */
	public function buildForm( FormBuilderInterface $builder, array $options ) {
		$builder
			->add( 'tipoRelacionPersona',
				null,
				[
					'label' => 'Tipo Relación'
				] )
			->add( 'estudiosCursados',
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
			->add( 'conviveConDeclarante' )
			->add( 'personaACargo',
				Select2EntityType::class,
				[
					'remote_route' => 'get_persona_por_dni',
					'class'        => Persona::class,
					'required'     => false,
					'placeholder'  => 'Por DNI'

				] );
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions( OptionsResolver $resolver ) {
		$resolver->setDefaults( array(
			'data_class' => 'App\Entity\PersonaACargo'
		) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix() {
		return 'app_personaacargo';
	}


}
