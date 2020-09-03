<?php

namespace App\Form;

use App\Entity\OrdenDelDia;
use Doctrine\ORM\EntityRepository;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use App\Form\GiroType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
//use UtilBundle\Form\Type\BootstrapCollectionType;
use Symfony\Component\Validator\Constraints\Valid;

class DictamenODIncorporarType extends AbstractType {
	/**
	 * {@inheritdoc}
	 */
	public function buildForm( FormBuilderInterface $builder, array $options ) {
		$builder
			->add( 'ordenDelDia',
				EntityType::class,
				[
					'label'         => 'Sesión',
					'class'         => OrdenDelDia::class,
					'required'      => true,
					'placeholder'   => 'Seleccionar',
					'query_builder' => function ( EntityRepository $er ) {
						return $er->createQueryBuilder( 'od' )
						          ->join( 'od.sesion', 'sesion' )
						          ->orderBy( 'sesion.fecha', 'DESC' );
					},
				] )
			->add( 'dictamen', AsignarDictamenAExpteType::class )
			->add( 'extracto',
				CKEditorType::class,
				[
					'required' => true,
					'config'   => array(
						'uiColor' => '#ffffff',
					)
				] )
			->add( 'aprobadoSobreTabla' )
			->add( 'vueltaAComision' );
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions( OptionsResolver $resolver ) {
		$resolver->setDefaults( array(
			'data_class'  => 'App\Entity\DictamenOD',
			'constraints' => new Valid()
		) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix() {
		return 'appbundle_dictamenod_incorporar';
	}


}
