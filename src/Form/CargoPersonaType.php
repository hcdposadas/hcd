<?php

namespace App\Form;

use App\Repository\ComisionRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CargoPersonaType extends AbstractType {
	/**
	 * {@inheritdoc}
	 */
	public function buildForm( FormBuilderInterface $builder, array $options ) {
		$builder
			->add( 'cargo')
			->add( 'areaAdministrativa')
			->add( 'comision', EntityType::class, [
				'class' => 'App\Entity\Comision',
				'query_builder' => function (ComisionRepository $er) {
					return $er->createQueryBuilder('c')
						->where('c.activo = :activo')
						->setParameter('activo', true);
				}
			])
		;
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions( OptionsResolver $resolver ) {
		$resolver->setDefaults( array(
			'data_class' => 'App\Entity\CargoPersona'
		) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix() {
		return 'App_cargopersona';
	}


}
