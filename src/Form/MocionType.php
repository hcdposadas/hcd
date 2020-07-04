<?php

namespace App\Form;

use App\Entity\Mocion;
use App\Entity\Parametro;
use App\Entity\Sesion;
use App\Entity\TipoMayoria;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\EntityRepository;
use App\Entity\Expediente;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MocionType extends AbstractType {
	/**
	 * @var EntityManagerInterface
	 */
	private $em;

	public function __construct( EntityManagerInterface $em ) {
		$this->em = $em;
	}

	/**
	 * {@inheritdoc}
	 */
	public function buildForm( FormBuilderInterface $builder, array $options ) {
		$siguienteNumero = $this->em->getRepository( Mocion::class )->siguienteNumero();

		$sesion = null;

		$builder
			->add( 'sesion',
				EntityType::class,
				[
					'label'         => 'Sesión',
					'class'         => Sesion::class,
					'data'          => $sesion,
					'query_builder' => function ( EntityRepository $er ) {
						return $er->createQueryBuilder( 's' )
						          ->where( 's.activo = true' )
						          ;
					},

				] )
			->add( 'numero',
				IntegerType::class,
				array(
					'disabled' => true,
					'label'    => 'Número',
					'data'     => $siguienteNumero,
				) )
			->add( 'tipo',
				EntityType::class,
				array(
					'label'         => 'Tipo de moción',
					'class'         => Parametro::class,
					'query_builder' => $this->em->getRepository( Parametro::class )->grupo( 'mocion-tipo' ),
				) )
			->add( 'tipoMayoria',
				EntityType::class,
				array(
					'label' => 'Tipo de mayoría',
					'class' => TipoMayoria::class,
					'query_builder' => function ( EntityRepository $er ) {
						return $er->createQueryBuilder( 'tm' )
						          ->orderBy('tm.id', 'ASC')
							;
					},
				) )
			->add( 'expediente',
				EntityType::class,
				array(
					'required' => false,
					'label'    => 'Expediente',
					'class'    => Expediente::class,
				) );
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
		return 'App_mocion';
	}


}
