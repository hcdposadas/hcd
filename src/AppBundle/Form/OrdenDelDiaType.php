<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use UtilBundle\Form\Type\BootstrapCollectionType;

class OrdenDelDiaType extends AbstractType {
	/**
	 * {@inheritdoc}
	 */
	public function buildForm( FormBuilderInterface $builder, array $options ) {
		$builder
			->add( 'dictamenes',
				BootstrapCollectionType::class,
				[
					'entry_type'    => DictamenODType::class,
					'allow_add'     => true,
					'allow_delete'  => true,
					'by_reference'  => false,
					'extra_actions' => [
						[ 'icon' => 'fa-file-text-o', 'class' => 'btn-editar-extracto', 'title' => 'Editar Extracto' ]
					]
				] )
			->add( 'cerrado' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions( OptionsResolver $resolver ) {
		$resolver->setDefaults( array(
			'data_class' => 'AppBundle\Entity\OrdenDelDia'
		) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix() {
		return 'appbundle_ordendeldia';
	}


}
