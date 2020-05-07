<?php

namespace AppBundle\Form;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use UtilBundle\Form\Type\Select2EntityType;

class DecretoType extends AbstractType {
	/**
	 * {@inheritdoc}
	 */
	public function buildForm( FormBuilderInterface $builder, array $options ) {
		$builder->add( 'numero' )
		        ->add( 'cuerpo',
			        CKEditorType::class,
			        [
				        'required' => true,
				        'config'   => array(
					        'uiColor' => '#ffffff',
				        ),
			        ] )
		        ->add( 'visible' )
		        ->add( 'activo' )
		        ->add( 'expediente', Select2EntityType::class,
				[
					'remote_route' => 'get_expedientes',
					'class'        => 'MesaEntradaBundle\Entity\Expediente',
					'required'     => false,
					'placeholder'  => 'Por Expte'

				] )
		        ->add( 'tipoDecreto' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions( OptionsResolver $resolver ) {
		$resolver->setDefaults( array(
			'data_class' => 'AppBundle\Entity\Decreto'
		) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix() {
		return 'appbundle_decreto';
	}


}
