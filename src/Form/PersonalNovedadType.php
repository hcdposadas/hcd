<?php

namespace App\Form;

use App\Entity\PersonalNovedad;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;

class PersonalNovedadType extends AbstractType {
	public function buildForm( FormBuilderInterface $builder, array $options ) {
		$builder
			->add( 'fecha',
				DateType::class,
				[
					'widget' => 'single_text',
					'html5'  => true
				] )
			->add( 'observacion' )
			->add( 'archivoFile',
				VichFileType::class,
				[
					'label'        => 'Archivo',
					'required'     => false,
					'allow_delete' => true, // optional, default is true
					'download_uri' => true, // optional, default is true
				] );
	}

	public function configureOptions( OptionsResolver $resolver ) {
		$resolver->setDefaults( [
			'data_class' => PersonalNovedad::class,
		] );
	}
}
