<?php

namespace App\Form;

use App\Entity\Dependencia;
use App\Entity\Persona;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;
use Vich\UploaderBundle\Form\Type\VichFileType;

class ExpedienteAdministrativoType extends AbstractType {
	/**
	 * {@inheritdoc}
	 */
	public function buildForm( FormBuilderInterface $builder, array $options ) {
		$builder
			->add( 'tipoExpediente' )
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
					'html5'  => true,
					'widget' => 'single_text',
				) )
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
		return 'App_expediente_administrativo';
	}


}
