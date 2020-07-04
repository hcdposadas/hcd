<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;
use Symfony\Component\Validator\Constraints\Valid;

class FirmanteDictamenType extends AbstractType {
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
					'attr'         => [ 'class' => '' ],
					'label'        => 'Concejal'

				] )
			->add( 'presidente' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions( OptionsResolver $resolver ) {
		$resolver->setDefaults( array(
			'data_class' => 'App\Entity\FirmanteDictamen',
			'constraints' => new Valid()
		) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix() {
		return 'App_firmantedictamen';
	}


}
