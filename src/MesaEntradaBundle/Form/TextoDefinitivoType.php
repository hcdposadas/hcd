<?php

namespace MesaEntradaBundle\Form;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use UtilBundle\Form\Type\BootstrapCollectionType;

class TextoDefinitivoType extends AbstractType {
	/**
	 * {@inheritdoc}
	 */
	public function buildForm( FormBuilderInterface $builder, array $options ) {
		$builder
			->add( 'texto',
				CKEditorType::class,
				[
					'required' => true,
					'config'   => array(
						'uiColor' => '#ffffff',
					),
					'attr'     => [ 'class' => 'texto_por_defecto' ]
				] )
			->add( 'numero' )
			->add( 'rama' )
			->add( 'anexos',
				BootstrapCollectionType::class,
				[
					'entry_type'   => AnexoTextoDefinitivoType::class,
					'allow_add'    => true,
					'allow_delete' => true,
					'by_reference' => false,
					'label'        => 'Anexos'
				] )
			->add( 'activo' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions( OptionsResolver $resolver ) {
		$resolver->setDefaults( array(
			'data_class' => 'MesaEntradaBundle\Entity\TextoDefinitivo'
		) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix() {
		return 'mesaentradabundle_textodefinitivo';
	}


}
