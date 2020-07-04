<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Form\BootstrapCollectionType;

class PersonalType extends AbstractType {
	/**
	 * {@inheritdoc}
	 */
	public function buildForm( FormBuilderInterface $builder, array $options ) {
		$builder->add( 'nombre' )
		        ->add( 'apellido' )
		        ->add( 'dni',
			        TextType::class,
			        [
				        'label' => 'DNI'
			        ] )
		        ->add( 'fechaNacimiento',
			        DateType::class,
			        array(
				        'widget' => 'single_text',
				        'format' => 'dd/MM/yyyy',
				        'attr'   => array(
					        'class' => 'datepicker',
				        ),
			        ) )
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
				        'max_items_add' => 1

			        ] )
		        ->add( 'personaACargo',
			        BootstrapCollectionType::class,
			        [
				        'entry_type' => PersonaACargoType::class,
				        'allow_add'  => true

			        ] );
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions( OptionsResolver $resolver ) {
		$resolver->setDefaults( array(
			'data_class' => 'App\Entity\Persona'
		) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix() {
		return 'App_persona';
	}


}
