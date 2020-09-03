<?php

namespace App\Form\Filter;

use App\Entity\Parametro;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SesionFilterType extends AbstractType {
	public function buildForm( FormBuilderInterface $builder, array $options ) {
		$builder->add( 'titulo' )
		        ->add( 'fecha',
			        DateType::class,
			        [
				        'widget' => 'single_text',
				        'html5'  => true
			        ] )
		        ->add( 'tipo',
			        EntityType::class,
			        [
				        'class'         => Parametro::class,
				        'label'         => 'Tipo',
				        'required'      => false,
				        'query_builder' => function ( EntityRepository $er ) {
					        return $er->createQueryBuilder( 'p' )
					                  ->where( 'p.grupo = :grupo' )
					                  ->setParameter( 'grupo', 'sesion-tipo' );
				        },
			        ] )
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

	public function configureOptions( OptionsResolver $resolver ) {
		$resolver->setDefaults( array(
			'required' => false
		) );
	}

	public function getBlockPrefix() {
		return 'app_bundle_sesion_filter';
	}
}
