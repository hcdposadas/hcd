<?php

namespace App\Form\Filter;


use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class DecretoFilterType extends AbstractType {
	/**
	 * {@inheritdoc}
	 */
	public function buildForm( FormBuilderInterface $builder, array $options ) {
		$builder->add( 'numero' )
		        ->add( 'expediente' )
		        ->add( 'tipoDecreto' )
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
//			'data_class' => 'App\Entity\Decreto'
			'required' => false
		) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix() {
		return 'App_decreto_filter';
	}


}
