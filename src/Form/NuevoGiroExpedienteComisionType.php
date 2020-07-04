<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Form\BootstrapCollectionType;

class NuevoGiroExpedienteComisionType extends AbstractType {
	/**
	 * {@inheritdoc}
	 */
	public function buildForm( FormBuilderInterface $builder, array $options ) {
		$builder
//			->add( 'giros',
//				BootstrapCollectionType::class,
//				[
//					'entry_type'      => GiroType::class,
//					'allow_add'       => true,
//					'allow_delete'    => true,
//					'by_reference'    => false,
//					'display_history' => false
//				] )
			->add( 'numeroNota' )
			->add( 'giroAdministrativos',
				BootstrapCollectionType::class,
				[
					'entry_type'      => GiroDEMType::class,
					'allow_add'       => true,
					'allow_delete'    => true,
					'by_reference'    => false,
					'display_history' => false,
					'label'           => 'Giros'
				] );
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions( OptionsResolver $resolver ) {
		$resolver->setDefaults( array(
			'data_class' => 'App\Entity\Expediente'
		) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix() {
		return 'App_expediente_nuevo_giro_expediente_comision';
	}


}
