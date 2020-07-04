<?php

namespace App\Form\Filter;

use App\Entity\Rama;
use App\Entity\Sesion;

use Doctrine\ORM\EntityRepository;

use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class TextoDefinitivoFilterType extends AbstractType {
	/**
	 * {@inheritdoc}
	 */
	public function buildForm( FormBuilderInterface $builder, array $options ) {
		$builder
			->add( 'numero' )
			->add( 'rama',
				EntityType::class,
				[
					'label'         => 'Rama',
					'class'         => Rama::class,
					'placeholder'   => 'Seleccionar',
					'query_builder' => function ( EntityRepository $er ) {
						$qb = $er->createQueryBuilder( 'r' );
						$qb->orderBy( 'r.orden', 'ASC' );

						return $qb;
					}
				] )
			->add( 'expedientesAdjuntos' )
			->add( 'aprobadoEnSesion',
				EntityType::class,
				[
					'label'        => 'Sesión',
					'class'        => Sesion::class,
					'placeholder'   => 'Seleccionar',
					'choice_label' => 'tituloLargo',
					'attr'         => [ 'class' => 'select2' ]
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
//			'data_class' => 'MesaEntradaBundle\Entity\TextoDefinitivo'
			'required' => false
		) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix() {
		return 'mesaentradabundle_textodefinitivo_filter';
	}


}
