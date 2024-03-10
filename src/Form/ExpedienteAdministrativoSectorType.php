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
use FOS\CKEditorBundle\Form\Type\CKEditorType;

class ExpedienteAdministrativoSectorType extends AbstractType {
	/**
	 * {@inheritdoc}
	 */
	public function buildForm( FormBuilderInterface $builder, array $options ) {
		$builder
			->add( 'texto',
			CKEditorType::class,
			[
				'required' => false,
				'config'   => array(
					'uiColor' => '#ffffff',
//						'height'  => '600px'
				),
				'attr'     => [ 'class' => 'texto_por_defecto' ]
			] )

			->add( 'anexos',
				BootstrapCollectionType::class,
				[
					'entry_type'   => AnexoExpedienteType::class,
					'required' => true,
					'allow_add'    => true,
					'allow_delete' => true,
					'by_reference' => false,
					'label'        => 'Anexos'
				] )
			->add( 'giroAdministrativos',
				BootstrapCollectionType::class,
				[
					'entry_type'   => GiroNuevoAdministrativoType::class,
					'allow_add'    => true,
					'allow_delete' => true,
					'by_reference' => false,
				] )
				->add( 'expedienteInternoFile',
				VichFileType::class,
				[
					'label'        => 'Archivo Expediente',
					'required'     => true,
					'allow_delete' => true, // optional, default is true
					'download_uri' => true, // optional, default is true
				] )
				->add( 'extracto',
				TextareaType::class,
				[
					'attr' => [ 'rows' => 3 ]
				] )
				;
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
