<?php

namespace App\Form;

use App\Entity\Persona;
use Symfony\Component\Form\AbstractType;

use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class PersonalType extends AbstractType {
	/**
	 * {@inheritdoc}
	 */
	public function buildForm( FormBuilderInterface $builder, array $options ) {
		$builder->add( 'nombre' )
		        ->add( 'apellido' )
		        ->add( 'genero',
			        ChoiceType::class,
			        [
				        'label'   => 'Género',
				        'choices' => [
					        Persona::PERSONA_GENERO_FEMENINO  => Persona::PERSONA_GENERO_FEMENINO,
					        Persona::PERSONA_GENERO_NE        => Persona::PERSONA_GENERO_NE,
					        Persona::PERSONA_GENERO_MASCULINO => Persona::PERSONA_GENERO_MASCULINO,
				        ],
			        ] )
		        ->add( 'tipoDocumento',
			        ChoiceType::class,
			        [
				        'choices' => [
					        Persona::PERSONA_TIPO_DOCUMENTO_DNI => Persona::PERSONA_TIPO_DOCUMENTO_DNI,
					        Persona::PERSONA_TIPO_DOCUMENTO_CI  => Persona::PERSONA_TIPO_DOCUMENTO_CI,
					        Persona::PERSONA_TIPO_DOCUMENTO_LC  => Persona::PERSONA_TIPO_DOCUMENTO_LC,
					        Persona::PERSONA_TIPO_DOCUMENTO_LE  => Persona::PERSONA_TIPO_DOCUMENTO_LE,
				        ],
			        ] )
		        ->add( 'dni',
			        TextType::class,
			        [
				        'label' => 'Número'
			        ] )
		        ->add( 'fechaNacimiento',
			        DateType::class,
			        [
				        'widget' => 'single_text',
				        'html5'  => true
			        ] )
		        ->add( 'legajo', LegajoType::class )
		        ->add( 'cargoPersona',
			        BootstrapCollectionType::class,
			        [
				        'entry_type'    => CargoPersonaType::class,
				        'allow_add'     => true,
				        'by_reference'  => false,
				        'max_items_add' => 1

			        ] )
		        ->add( 'contactoPersona',
			        BootstrapCollectionType::class,
			        [
				        'entry_type'    => ContactoPersonaType::class,
				        'allow_add'     => true,
				        'max_items_add' => 1,
				        'by_reference'  => false,
			        ] )
		        ->add( 'domicilioPersona',
			        BootstrapCollectionType::class,
			        [
				        'entry_type'    => DomicilioPersonaType::class,
				        'allow_add'     => true,
				        'by_reference'  => false,
				        'max_items_add' => 1

			        ] );
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions( OptionsResolver $resolver ) {
		$resolver->setDefaults( array(
			'data_class' => Persona::class
		) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix() {
		return 'app_persona';
	}


}
