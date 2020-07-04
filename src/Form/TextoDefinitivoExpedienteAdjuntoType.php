<?php

namespace App\Form;

use App\Entity\Expediente;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

//use UtilBundle\Form\Type\Select2EntityType;

class TextoDefinitivoExpedienteAdjuntoType extends AbstractType {
	/**
	 * {@inheritdoc}
	 */
	public function buildForm( FormBuilderInterface $builder, array $options ) {
		$builder->add( 'expediente',
			Select2EntityType::class,
			[
				'remote_route' => 'get_proyectos_bae',
				'class'        => Expediente::class,
				'required'     => false,
				'placeholder'  => 'Por Expte'

			] );
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions( OptionsResolver $resolver ) {
		$resolver->setDefaults( array(
			'data_class' => 'App\Entity\TextoDefinitivoExpedienteAdjunto'
		) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix() {
		return 'appbundle_textodefinitivoexpedienteadjunto';
	}


}
