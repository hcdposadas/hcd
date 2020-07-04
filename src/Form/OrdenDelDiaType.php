<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Form\BootstrapCollectionType;

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
//					'extra_actions' => [
//						[ 'icon' => 'fa-file-text-o', 'class' => 'btn-editar-extracto', 'title' => 'Editar Extracto' ]
//					]
				] )
		;
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions( OptionsResolver $resolver ) {
		$resolver->setDefaults( array(
			'data_class' => 'App\Entity\OrdenDelDia'
		) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix() {
		return 'App_ordendeldia';
	}


}
