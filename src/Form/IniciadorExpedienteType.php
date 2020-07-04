<?php

namespace App\Form;

use App\Entity\Cargo;
use Doctrine\ORM\EntityRepository;
use App\Entity\Iniciador;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

class IniciadorExpedienteType extends AbstractType {
	/**
	 * {@inheritdoc}
	 */
	public function buildForm( FormBuilderInterface $builder, array $options ) {
		$builder
			->add( 'iniciador',
				Select2EntityType::class,
				[
					'remote_route' => 'get_cargos_por_nombre',
					'class'        => 'App\Entity\Iniciador',
					'required'     => false,
					'placeholder'  => 'Por Nombre',
					'attr'         => [ 'class' => 'select-iniciador' ],
					'label'        => 'Concejal'

				] )

//	        ->add( 'iniciador',
//		        EntityType::class,
//		        [
//			        'class'         => Iniciador::class,
//			        'query_builder' => function ( EntityRepository $er ) {
//				        $qb = $er->createQueryBuilder( 'i' );
//				        $qb->join( 'i.cargoPersona', 'cp' )
//				           ->join( 'cp.persona', 'p' )
//				           ->join( 'cp.cargo', 'c' )
//				           ->where( 'cp.activo = true' )
//				           ->andWhere( 'p.activo = true' )
//				           ->andWhere( 'i.activo = true' )
//				           ->andWhere(
//					           $qb->expr()->andX(
//						           $qb->expr()->orX(
//							           $qb->expr()->eq( 'c.id', ':id' ),
//							           $qb->expr()->eq( 'c.id', ':iddefensor' )
//						           )
//					           )
//				           )
//				           ->setParameter( 'id',
//					           Cargo::CARGO_CONCEJAL )
//				           ->setParameter( 'iddefensor',
//					           Cargo::CARGO_DEFENSOR );
//
//				        return $qb;
//			        },
//			        'attr'          => [ 'class' => 'select2 select-iniciador' ],
//			        'required'      => false
//		        ] )
		;
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions( OptionsResolver $resolver ) {
		$resolver->setDefaults( array(
			'data_class' => 'App\Entity\IniciadorExpediente'
		) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix() {
		return 'App_iniciadorexpediente';
	}


}
