<?php

namespace MesaEntradaBundle\Form;

use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use UtilBundle\Form\Type\BootstrapCollectionType;
use UtilBundle\Form\Type\Select2EntityType;

class ExpedienteLegislativoExternoType extends AbstractType {
	public function buildForm( FormBuilderInterface $builder, array $options ) {
		$builder
			->add( 'expediente' )
			->add( 'tipoProyecto',
				null,
				[
					'required'    => false,
					'placeholder' => 'Seleccionar',
					'attr'        => [ 'class' => 'select2' ]
				] )
			->add( 'letra' )
			->add( 'periodoLegislativo',
				null,
				[
					'attr'     => [ 'class' => 'select2' ],
					'required' => true
				] )
			->add( 'extracto',
				TextareaType::class,
				[
					'attr' => [ 'rows' => 5 ]
				] )
			->add( 'texto',
				CKEditorType::class,
				[
					'required' => true,
					'config'   => array(
						'uiColor' => '#ffffff',
//						'height'  => '600px'
					),
					'attr'     => [ 'class' => 'texto_por_defecto' ]
				] )
			->add( 'iniciadores',
				BootstrapCollectionType::class,
				[
					'entry_type'   => IniciadorExpedienteExternoType::class,
					'allow_add'    => true,
					'allow_delete' => true,
					'by_reference' => false,
					'label'        => 'Iniciadores'
				] )
			->add( 'dependencia',
				Select2EntityType::class,
				[
					'remote_route' => 'get_dependencias_por_nombre',
					'class'        => 'AppBundle\Entity\Dependencia',
					'required'     => false,
					'placeholder'  => 'Por Nombre'

				] )
			->add( 'fecha',
				DateType::class,
				array(
					'widget' => 'single_text',
					'html5'  => true
//					'format' => 'dd/MM/yyyy',
				) )
			->add( 'giros',
				BootstrapCollectionType::class,
				[
					'entry_type'   => GiroType::class,
					'allow_add'    => true,
					'allow_delete' => true,
					'by_reference' => false,
				] )
			->add( 'giroAdministrativos',
				BootstrapCollectionType::class,
				[
					'entry_type'   => GiroAdministrativoType::class,
					'allow_add'    => true,
					'allow_delete' => true,
					'by_reference' => false,
				] )
			->add( 'expedientesAdjunto',
				BootstrapCollectionType::class,
				[
					'entry_type'   => ExpedienteAdjuntoType::class,
					'allow_add'    => true,
					'allow_delete' => true,
					'by_reference' => false,
				] )
			->add( 'proyectoDem',
				CheckboxType::class,
				[
					'label' => 'Es Proyecto DEM',
					'required' => false
				] )
			->add( 'guardar',
				SubmitType::class,
				array(
					'attr' => array( 'class' => 'btn btn-primary' ),
				) );
	}

	public function configureOptions( OptionsResolver $resolver ) {
		$resolver->setDefaults( array(
			'data_class' => 'MesaEntradaBundle\Entity\Expediente',
		) );
	}

	public function getBlockPrefix() {
		return 'mesa_entrada_bundle_expediente_legislativo_externo_type';
	}
}
