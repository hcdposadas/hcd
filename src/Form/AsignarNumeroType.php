<?php

namespace App\Form;

use App\Entity\PeriodoLegislativo;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AsignarNumeroType extends AbstractType {
	/**
	 * {@inheritdoc}
	 */
	public function buildForm( FormBuilderInterface $builder, array $options ) {
		$builder
			->add( 'expediente',
				TextType::class,
				[
					'required' => true
				] )
			->add( 'letra',
				TextType::class,
				[
					'required' => true
				] )
//			->add( 'periodoLegislativo',
//				EntityType::class,
//				[
//					'class'    => PeriodoLegislativo::class,
//					'disabled' => true
//				] )
			->add( 'asignar',
				SubmitType::class,
				[
					'attr' => [
						'class'          => 'btn btn-primary',
					],
				] )
		;
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions( OptionsResolver $resolver ) {
		$resolver->setDefaults( array(
			'data_class' => 'App\Entity\Expediente',
		) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix() {
		return 'App_expediente_asignar_numero';
	}


}
