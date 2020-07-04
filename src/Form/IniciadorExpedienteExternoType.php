<?php

namespace App\Form;

use App\Entity\Iniciador;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

class IniciadorExpedienteExternoType extends AbstractType {
	/**
	 * {@inheritdoc}
	 */
	public function buildForm( FormBuilderInterface $builder, array $options ) {
		$builder
//			->add('iniciador')
			->add( 'iniciador',
				Select2EntityType::class,
				[
					'remote_route' => 'get_cargos_por_nombre_legacy',
					'class'        => Iniciador::class,
					'required'     => false,
					'placeholder'  => 'Por Nombre',
					'attr'         => [ 'class' => '' ],
					'label'        => 'Concejal',
					'language'      => 'es',
				] )
			->add( 'autor',
				CheckboxType::class,
				[
					'attr'     => [ "class"=>'check-autor' ],
					'required' => false
				] );
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions( OptionsResolver $resolver ) {
		$resolver->setDefaults( array(
			'data_class' => 'App\Entity\IniciadorExpediente'
		) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix() {
		return 'App_iniciadorexpediente_externo';
	}


}
