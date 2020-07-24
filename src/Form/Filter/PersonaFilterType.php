<?php

namespace App\Form;

use App\Entity\Persona;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PersonaFilterType extends AbstractType {
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
			        [
				        'html5'  => true,
				        'widget' => 'single_text',
			        ] )
		        ->add( 'buscar',
			        SubmitType::class,
			        [
				        'attr' => array( 'class' => 'btn btn-primary' ),
			        ] )
		        ->add( 'limpiar',
			        ResetType::class,
			        [
				        'attr' => array( 'class' => 'btn btn-default reset' ),
			        ] );
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions( OptionsResolver $resolver ) {
		$resolver->setDefaults( array(
//			'data_class' => Persona::class,
			'required'   => false
		) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix() {
		return 'app_persona_filter';
	}


}
