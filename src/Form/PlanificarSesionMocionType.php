<?php

namespace App\Form;


use App\Entity\Parametro;
use App\Entity\TipoMayoria;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PlanificarSesionMocionType extends AbstractType {

	/**
	 * {@inheritdoc}
	 */
	public function buildForm( FormBuilderInterface $builder, array $options ) {


		$builder
			->add( 'texto' )
			->add( 'numero',
				IntegerType::class,
				[
//					'disabled' => true,
					'label'    => 'Número'
				] )
//			->add( 'tipo',
//				EntityType::class,
//				array(
//					'label'         => 'Tipo de moción',
//					'class'         => Parametro::class,
//					'query_builder' => function ( EntityRepository $er ) {
//						return $er->grupo( 'mocion-tipo' );
//					}
//				) )
			->add( 'tipoMayoria',
				EntityType::class,
				[
					'label'         => 'Tipo de mayoría',
					'class'         => TipoMayoria::class,
					'query_builder' => function ( EntityRepository $er ) {
						return $er->createQueryBuilder( 'tm' )
						          ->orderBy( 'tm.id', 'ASC' );
					},
				] );
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions( OptionsResolver $resolver ) {
		$resolver->setDefaults( array(
			'data_class' => 'App\Entity\Mocion'
		) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix() {
		return 'app_planificar_sesion_mocion';
	}


}
