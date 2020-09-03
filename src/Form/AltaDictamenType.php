<?php

namespace App\Form;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use App\Form\FirmanteDictamenType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Form\BootstrapCollectionType;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

class AltaDictamenType extends AbstractType {
	public function buildForm( FormBuilderInterface $builder, array $options ) {

		$builder
//			->add( 'expediente',
//				Select2EntityType::class,
//				[
//					'remote_route' => 'get_expedientes',
//					'class'        => 'App\Entity\Expediente',
//					'required'     => true,
//					'placeholder'  => 'Por Expte'
//
//				] )
			->add( 'fecha',
				DateType::class,
				array(
					'widget' => 'single_text',
					'html5'  => true
				) )
			->add( 'expediente', AltaExpedienteDictamenType::class )
			->add( 'tipoProyecto',
				null,
				[
					'label'       => 'Tipo Dictamen',
					'required'    => true,
					'placeholder' => 'Seleccionar',
					'attr'        => [ 'class' => 'tipo-proyecto select2' ]
				] )
			->add( 'periodoLegislativo',
				null,
				[
					'attr' => [ 'class' => 'select2' ]
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
				] )
			->add( 'anexos',
				BootstrapCollectionType::class,
				[
					'entry_type'   => AnexoDictamenType::class,
					'allow_add'    => true,
					'allow_delete' => true,
					'by_reference' => false,
					'label'        => 'Anexos'
				] )
			->add( 'firmantes',
				BootstrapCollectionType::class,
				[
					'entry_type'   => FirmanteDictamenType::class,
					'allow_add'    => true,
					'allow_delete' => true,
					'by_reference' => false,
//					'display_history' => false,
					'label'        => 'Firmantes'
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
