<?php

namespace MesaEntradaBundle\Form;

use AppBundle\Entity\PeriodoLegislativo;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AsignarNumeroType extends AbstractType {
	/**
	 * {@inheritdoc}
	 */
	public function buildForm( FormBuilderInterface $builder, array $options ) {
		$builder
			->add( 'expediente' )
			->add( 'letra' )
//			->add( 'periodoLegislativo',
//				EntityType::class,
//				[
//					'class'    => PeriodoLegislativo::class,
//					'disabled' => true
//				] )
			->add( 'asignar',
				SubmitType::class,
				[
					'attr' => [ 'class' => 'btn btn-primary' ]
				] );
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions( OptionsResolver $resolver ) {
		$resolver->setDefaults( array(
			'data_class' => 'MesaEntradaBundle\Entity\Expediente',
		) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix() {
		return 'mesaentradabundle_expediente';
	}


}
