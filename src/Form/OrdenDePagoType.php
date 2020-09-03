<?php

namespace App\Form;

use App\Entity\Decreto;
use App\Entity\PeriodoLegislativo;
use App\Entity\TipoOrdenPago;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\File;
use App\Form\BootstrapCollectionType;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Vich\UploaderBundle\Form\Type\VichImageType;

class OrdenDePagoType extends AbstractType {
	/**
	 * {@inheritdoc}
	 */
	public function buildForm( FormBuilderInterface $builder, array $options ) {
		$builder
			->add( 'tipoOrdenPago',
				EntityType::class,
				[
					'class'       => TipoOrdenPago::class,
					'required'    => false,
					'placeholder' => 'Seleccionar',
				] )
			->add( 'numero',
				NumberType::class,
				[
					'required' => true,
					'label'    => 'Número'
				] )
			->add( 'periodoLegislativo',
				EntityType::class,
				[
					'class' => PeriodoLegislativo::class,
					'label' => 'Año'
				] )
			->add( 'fechaRendicion',
				BootstrapCollectionType::class,
				[
					'entry_type'   => OrdenDePagoRendicionType::class,
					'allow_add'    => true,
					'allow_delete' => false,
					'by_reference' => false,
					'label'        => 'Fecha de Rendición'
				] )
			->add( 'folios' )
			->add( 'paginaInicio' )
			->add( 'paginaFin' )
			->add( 'tipoOrdenPago' )
			->add( 'numeroEstante' )
			->add( 'numeroCaja' )
			->add( 'decreto',
				Select2EntityType::class,
				[
					'remote_route' => 'get_decretos',
					'class'        => Decreto::class,
					'required'     => false,
					'placeholder'  => 'Por Nro'

				] )
			->add( 'ordenDePagoFile',
				VichFileType::class,
				[
					'label'       => 'Archivo',
					'required'    => false,
					'constraints' => [
						new File( [
							'maxSize'          => '75M',
							'mimeTypes'        => [
								'image/*',
								'application/pdf'
							],
							'mimeTypesMessage' => 'Solo se aceptan .jpg, .png, .jpeg, .pdf',
						] )
					]
				] )
			->add( 'observacion',
				TextareaType::class,
				[
					'label'    => 'Observación',
					'required' => false
				] )
			->add( 'activo' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions( OptionsResolver $resolver ) {
		$resolver->setDefaults( array(
			'data_class' => 'App\Entity\OrdenDePago'
		) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix() {
		return 'App_ordendepago';
	}


}
