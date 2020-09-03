<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Form\BootstrapCollectionType;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;
use Vich\UploaderBundle\Form\Type\VichFileType;

class ExpedienteType extends AbstractType {
	/**
	 * {@inheritdoc}
	 */
	public function buildForm( FormBuilderInterface $builder, array $options ) {
		$builder
			->add( 'tipoExpediente',
				null,
				[
					'attr' => [ 'class' => 'tipo-expediente select2' ]
				] )
			->add( 'textoDefinitivo' )
			->add( 'extracto',
				TextareaType::class,
				[
					'attr' => [ 'rows' => 5 ]
				] )
			->add( 'numeroNota' )
			->add( 'sesionNumero',
				NumberType::class,
				[
					'label'    => 'Nº Sesión',
					'required' => false
				] )
			->add( 'sesionAnio',
				NumberType::class,
				[
					'label'    => 'Año Sesión',
					'required' => false
				] )
			->add( 'expediente',
				null,
				[
					'label'    => 'N° expediente',
					'required' => true
				] )
			->add( 'periodoLegislativo',
				null,
				[
					'attr' => [ 'class' => 'select2' ]
				] )
			->add( 'anio',
				null,
				[
					'label'    => 'Año',
					'required' => false
				] )
			->add( 'letra' )
			->add( 'fecha',
				DateType::class,
				array(
					'widget' => 'single_text',
					'format' => 'dd/MM/yyyy',
					'attr'   => array(
						'class' => 'datepicker',
					),
				) )
			->add( 'registroMunicipal' )
			->add( 'expedienteInternoFile',
				VichFileType::class,
				[
					'label'        => 'Archivo Expediente',
					'required'     => false,
					'allow_delete' => true, // optional, default is true
					'download_uri' => true, // optional, default is true
				] )
			->add( 'expedienteExternoFile',
				VichFileType::class,
				[
					'label'        => 'Archivo Expediente',
					'required'     => false,
					'allow_delete' => true, // optional, default is true
					'download_uri' => true, // optional, default is true
				] )
			->add( 'iniciadores',
				BootstrapCollectionType::class,
				[
					'entry_type'   => IniciadorExpedienteType::class,
					'allow_add'    => true,
					'allow_delete' => true,
					'by_reference' => false,
				] )
//            ->add('iniciador',
//                Select2EntityType::class,
//                [
//                    'remote_route' => 'get_cargos_por_nombre',
//                    'class' => 'App\Entity\Iniciador',
//                    'required' => false,
//                    'placeholder' => 'Por Nombre'
//
//                ])
			->add( 'iniciadorParticular',
				Select2EntityType::class,
				[
					'remote_route' => 'get_persona_por_dni',
					'class'        => 'App\Entity\Persona',
					'required'     => false,
					'placeholder'  => 'Por DNI'

				] )
			->add( 'dependencia',
				Select2EntityType::class,
				[
					'remote_route' => 'get_dependencias_por_nombre',
					'class'        => 'App\Entity\Dependencia',
					'required'     => false,
					'placeholder'  => 'Por Nombre'

				] )
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
		return 'App_expediente';
	}


}
