<?php

namespace App\Form;

use App\Entity\PersonalArticulo;
use App\Entity\PersonalLicencia;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class PersonalLicenciaType extends AbstractType {
	public function buildForm( FormBuilderInterface $builder, array $options ) {
		$builder
			->add( 'fechaDesde',
				DateType::class,
				[
					'widget' => 'single_text',
					'html5'  => true
				] )
			->add( 'fechaHasta',
				DateType::class,
				[
					'widget' => 'single_text',
					'html5'  => true
				] )
			->add( 'fechaReincorporacion',
				DateType::class,
				[
					'widget' => 'single_text',
					'html5'  => true,
					'label'  => 'Se reincorpora el'
				] )
			->add( 'archivoFile',
				VichFileType::class,
				[
					'label'        => 'Archivo',
					'required'     => false,
					'allow_delete' => true, // optional, default is true
					'download_uri' => true, // optional, default is true
				] )
			->add( 'articulo', EntityType::class,
				[
					'label'        => 'Artículo segun estatuto',
					'class'        => PersonalArticulo::class,
					'placeholder'  => 'Seleccionar',
					'choice_label' => 'tituloLargo',
					'attr'         => [ 'class' => 'select2' ]
				]);
	}

	public function configureOptions( OptionsResolver $resolver ) {
		$resolver->setDefaults( [
			'data_class' => PersonalLicencia::class,
		] );
	}
}
