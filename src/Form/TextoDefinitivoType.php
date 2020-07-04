<?php

namespace App\Form;

use App\Entity\Rama;
use App\Entity\Sesion;
use App\Form\AsignarDictamenAExpteType;
use App\Form\TextoDefinitivoExpedienteAdjuntoType;
use Doctrine\ORM\EntityRepository;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
//use UtilBundle\Form\Type\BootstrapCollectionType;

class TextoDefinitivoType extends AbstractType {
	/**
	 * {@inheritdoc}
	 */
	public function buildForm( FormBuilderInterface $builder, array $options ) {
		$builder
			->add( 'dictamen', AsignarDictamenAExpteType::class )
			->add( 'texto',
				CKEditorType::class,
				[
					'required' => true,
					'config'   => array(
						'uiColor' => '#ffffff',
					),
					'attr'     => [ 'class' => 'texto_por_defecto' ]
				] )
			->add( 'numero' )
			->add( 'rama',
				EntityType::class,
				[
					'label'         => 'Rama',
					'placeholder'   => 'Seleccionar',
					'required'      => false,
					'class'         => Rama::class,
					'query_builder' => function ( EntityRepository $er ) {
						$qb = $er->createQueryBuilder( 'r' );
						$qb->orderBy( 'r.orden', 'ASC' );

						return $qb;
					}
				] )
			->add( 'expedientesAdjuntos',
				BootstrapCollectionType::class,
				[
					'entry_type'   => TextoDefinitivoExpedienteAdjuntoType::class,
					'allow_add'    => true,
					'allow_delete' => true,
					'by_reference' => false,
					'label'        => 'Expedientes Adjuntos'
				] )
			->add( 'anexos',
				BootstrapCollectionType::class,
				[
					'entry_type'   => AnexoTextoDefinitivoType::class,
					'allow_add'    => true,
					'allow_delete' => true,
					'by_reference' => false,
					'label'        => 'Anexos'
				] )
			->add( 'aprobadoEnSesion',
				EntityType::class,
				[
					'label'        => 'Aprobado en Sesión',
					'class'        => Sesion::class,
					'choice_label' => 'tituloLargo',
					'attr'         => [ 'class' => 'select2' ]
				] )
			->add( 'activo' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions( OptionsResolver $resolver ) {
		$resolver->setDefaults( array(
			'data_class' => 'App\Entity\TextoDefinitivo'
		) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix() {
		return 'mesaentradabundle_textodefinitivo';
	}


}
