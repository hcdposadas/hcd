<?php

namespace App\Form;

use App\Entity\Dependencia;
use App\Entity\Persona;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Form\BootstrapCollectionType;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

class ExpedienteAdministrativoExternoType extends AbstractType {
	public function buildForm( FormBuilderInterface $builder, array $options ) {
		$builder
			->add( 'expediente',
				TextType::class,
				[
					'required' => true
				] )
			->add( 'letra',
				TextType::class,
				[
					'required' => true
				] )
			->add( 'periodoLegislativo',
				null,
				[
					'attr' => [ 'class' => 'select2' ]
				] )
			->add( 'extracto',
				TextareaType::class,
				[
					'attr' => [ 'rows' => 5 ]
				] )
			->add( 'registroMunicipal' )
			->add( 'dependencia',
				Select2EntityType::class,
				[
					'remote_route' => 'get_dependencias_por_nombre',
					'class'        => Dependencia::class,
					'required'     => false,
					'placeholder'  => 'Por Nombre'

				] )
			->add( 'iniciadorParticular',
				Select2EntityType::class,
				[
					'remote_route' => 'get_persona_por_dni',
					'class'        => Persona::class,
					'required'     => false,
					'placeholder'  => 'Por DNI'

				] )
			->add( 'giroAdministrativos',
				BootstrapCollectionType::class,
				[
					'entry_type'   => GiroAdministrativoType::class,
					'allow_add'    => true,
					'allow_delete' => true,
					'by_reference' => false,
				] )
			->add( 'fecha',
				DateType::class,
				array(
					'widget' => 'single_text',
					'html5'  => true
//					'format' => 'dd/MM/yyyy',
				) )
			->add( 'guardar',
				SubmitType::class,
				array(
					'attr' => array( 'class' => 'btn btn-primary' ),
				) );
	}

	public function configureOptions( OptionsResolver $resolver ) {
		$resolver->setDefaults( array(
			'data_class' => 'App\Entity\Expediente',
		) );
	}

	public function getBlockPrefix() {
		return 'mesa_entrada_bundle_expediente_administrativo_externo_type';
	}
}
