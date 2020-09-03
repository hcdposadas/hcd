<?php

namespace App\Form;

use App\Entity\AreaAdministrativa;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GiroDEMType extends AbstractType {
	public function buildForm( FormBuilderInterface $builder, array $options ) {
		$builder->
		add( 'areaDestino',
			EntityType::class,
			[
				'class'         => AreaAdministrativa::class,
				'attr'          => [ 'class' => 'select2' ],
//				'query_builder' => function ( EntityRepository $er ) {
//					return $er->createQueryBuilder( 'aa' )
//					          ->where( 'aa.id = :id' )
//					          ->setParameter( 'id', AreaAdministrativa::AREA_ADMINISTRATIVA_DEM );
//				},
			]
		)
		        ->add( 'texto' )
		        ->add( 'fechaGiro',
			        DateType::class,
			        array(
				        'widget' => 'single_text',
				        'html5'  => true,
//				        'format' => 'dd/MM/yyyy',
				        'attr'   => array(
					        'class' => 'datepicker',
				        ),
			        ) );
	}

	public function configureOptions( OptionsResolver $resolver ) {
		$resolver->setDefaults( array(
			'data_class' => 'App\Entity\GiroAdministrativo'
		) );
	}

	public function getBlockPrefix() {
		return 'mesa_entrada_bundle_giro_demtype';
	}
}
