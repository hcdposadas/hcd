<?php

namespace App\Form\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class DictamenFilterType extends AbstractType {
	/**
	 * {@inheritdoc}
	 */
	public function buildForm( FormBuilderInterface $builder, array $options ) {
		$builder->add( 'expediente' )
		->add('estado', ChoiceType::class, [
			'label' => 'Estado',
			'mapped' => false,
			'required' => true,
			'choices' => [
				'TODOS' => 'TODOS',
				'APROBADOS' => true,
				'PENDIENTES' => null,
				'RECHAZADOS' => false,
			],
			'data' => 'TODOS',
		])
		        ->add( 'buscar',
			        SubmitType::class,
			        array(
				        'attr' => array( 'class' => 'btn btn-primary' ),
			        ) )
		        ->add( 'limpiar',
			        ResetType::class,
			        array(
				        'attr' => array( 'class' => 'btn btn-default reset' ),
			        ) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions( OptionsResolver $resolver ) {
		$resolver->setDefaults( array(
			'required' => false
		) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix() {
		return 'App_dictamen_filter';
	}


}
