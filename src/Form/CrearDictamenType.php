<?php

namespace App\Form;

use App\Entity\Expediente;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

class CrearDictamenType extends AbstractType {
	public function buildForm( FormBuilderInterface $builder, array $options ) {

		$builder
			->add( 'expediente',
				Select2EntityType::class,
				[
					'remote_route' => 'get_expedientes',
					'class'        => Expediente::class,
					'required'     => true,
					'placeholder'  => 'Por Expte'

				] )
			->add( 'textoDictamen',
				CKEditorType::class,
				[
					'required' => true,
					'config'   => array(
						'uiColor' => '#ffffff',
//						'height'  => '600px'
					),
					'attr'     => [ 'class' => 'texto_por_defecto' ]
				] );
	}

	public function configureOptions( OptionsResolver $resolver ) {
		$resolver->setDefaults( array(
			'data_class' => 'App\Entity\Dictamen'
		) );
	}

	public function getBlockPrefix() {
		return 'app_bundle_crear_dictamen_type';
	}
}
