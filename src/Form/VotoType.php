<?php

namespace App\Form;

use App\Entity\Usuario;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;


class VotoType extends AbstractType {
	/**
	 * {@inheritdoc}
	 */
	public function buildForm( FormBuilderInterface $builder, array $options ) {
		$builder
			->add( 'valor' )
//			->add('concejal')
//			->add( 'concejal',
//				EntityType::class,
//				[
//					'class'         => Usuario::class,
//					'query_builder' => function ( EntityRepository $er ) {
//						return $er->createQueryBuilder( 'c' )
//							->where('c.roles LIKE :roles')
//							->setParameter('roles', '%"ROLE_CONCEJAL"%');
////						->where($qb->expr()->isMemberOf('{ROLE_CONCEJAL}', 'u.roles'))
////						          ->where( 'c.roles IN (:roles)' )
////						          ->setParameter( 'roles', ['ROLE_CONCEJAL'] )
//							;
//					},
//
//				] )
			->add( 'mocion' )
			->add( 'votacion' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions( OptionsResolver $resolver ) {
		$resolver->setDefaults( array(
			'data_class' => 'App\Entity\Voto'
		) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix() {
		return 'App_voto';
	}


}
