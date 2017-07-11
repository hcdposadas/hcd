<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonaType extends AbstractType {
	/**
	 * {@inheritdoc}
	 */
	public function buildForm( FormBuilderInterface $builder, array $options ) {
		$builder->add( 'nombre' )
		        ->add( 'apellido' )
		        ->add( 'dni' )
		        ->add( 'fechaNacimiento',
			        DateType::class,
			        array(
				        'widget' => 'single_text',
				        'format' => 'dd/MM/yyyy',
				        'attr'   => array(
					        'class' => 'datepicker',
				        ),
			        ) )
		        ->add( 'telefono' )
		        ->add( 'celular' )
		        ->add( 'mail' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions( OptionsResolver $resolver ) {
		$resolver->setDefaults( array(
			'data_class' => 'AppBundle\Entity\Persona'
		) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix() {
		return 'appbundle_persona';
	}


}
