<?php

namespace MesaEntradaBundle\Form;

use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use UtilBundle\Form\Type\BootstrapCollectionType;
use UtilBundle\Form\Type\Select2EntityType;
use Vich\UploaderBundle\Form\Type\VichFileType;

class ProyectoType extends AbstractType {
	/**
	 * {@inheritdoc}
	 */
	public function buildForm( FormBuilderInterface $builder, array $options ) {
		$builder
			->add( 'tipoProyecto',
				null,
				[
					'required' => true,
					'attr'     => [ 'class' => 'tipo-proyecto select2' ]
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
			->add( 'iniciarComo',
				EntityType::class,
				[
					'class'    => 'MesaEntradaBundle\Entity\Iniciador',
					'required' => true,
					'mapped'   => false,
					'choices'  => $options['iniciarComo'],
				] )
			->add( 'iniciadores',
				BootstrapCollectionType::class,
				[
					'entry_type'   => IniciadorExpedienteType::class,
					'allow_add'    => true,
					'allow_delete' => true,
					'by_reference' => false,
					'label'        => 'Acompañantes'
				] )
			->add( 'fecha',
				DateType::class,
				array(
					'widget' => 'single_text',
					'format' => 'dd/MM/yyyy',
					'attr'   => array(
						'class' => 'datepicker',
					),
				) )
			->add( 'guardar',
				SubmitType::class,
				array(
					'attr' => array( 'class' => 'btn btn-primary' ),
				) )
			->add( 'guardarYEnviar',
				SubmitType::class,
				array(
					'attr' => array( 'class' => 'btn btn-primary' ),
				) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions( OptionsResolver $resolver ) {
		$resolver->setDefaults( array(
			'data_class'  => 'MesaEntradaBundle\Entity\Expediente',
			'iniciarComo' => null
		) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix() {
		return 'mesaentradabundle_expediente';
	}


}