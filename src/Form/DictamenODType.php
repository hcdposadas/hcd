<?php

namespace App\Form;

use App\Entity\Dictamen;
use App\Entity\DictamenOD;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;
use Symfony\Component\Validator\Constraints\Valid;

class DictamenODType extends AbstractType {
	/**
	 * {@inheritdoc}
	 */
	public function buildForm( FormBuilderInterface $builder, array $options ) {
		$builder
			->add( 'dictamen',
				Select2EntityType::class,
				[
					'remote_route' => 'get_dictamenes_od',
					'class'        => Dictamen::class,
					'required'     => false,
					'placeholder'  => 'Buscar por Expediente',
				] )
			->add( 'tieneTratamientoPreferencial',
				null,
				[
					'label' => 'Tratamiento Preferencial'
				] );
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions( OptionsResolver $resolver ) {
		$resolver->setDefaults( array(
			'data_class'  => DictamenOD::class,
			'constraints' => new Valid(),
		) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix() {
		return 'App_dictamenod';
	}


}
