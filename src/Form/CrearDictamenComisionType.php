<?php

namespace App\Form;

use App\Entity\Expediente;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;

class CrearDictamenComisionType extends AbstractType {
	public function buildForm( FormBuilderInterface $builder, array $options ) {

		$builder
        ->add( 'fecha',
        DateType::class,
        array(
            'widget' => 'single_text',
            'html5'  => true
        ) )
    ->add( 'tipoProyecto',
        null,
        [
            'label'       => 'Tipo Dictamen',
            'required'    => true,
            'placeholder' => 'Seleccionar',
            'attr'        => [ 'class' => 'tipo-proyecto select2' ]
        ] )
    ->add( 'periodoLegislativo',
        null,
        [
            'attr' => [ 'class' => 'select2' ]
        ] )/* 
    ->add( 'textoDictamen',
        CKEditorType::class,
        [
            'required' => false,
            'config'   => array(
                'uiColor' => '#ffffff',
//						'height'  => '600px'
            ),
            'attr'     => [ 'class' => 'texto_por_defecto' ]
        ] ) */
    ->add( 'dictamenFile',
        VichFileType::class,
        [
            'label'        => 'Dictamen Firmado',
            'required'     => false,
            'allow_delete' => true, // optional, default is true
            'download_uri' => true, // optional, default is true
        ] )
    ->add( 'anexos',
        BootstrapCollectionType::class,
        [
            'entry_type'   => AnexoDictamenType::class,
            'allow_add'    => true,
            'allow_delete' => true,
            'by_reference' => false,
            'label'        => 'Anexos'
        ] )
    ->add( 'firmantes',
        BootstrapCollectionType::class,
        [
            'entry_type'   => FirmanteDictamenType::class,
            'allow_add'    => true,
            'allow_delete' => true,
            'by_reference' => false,
//					'display_history' => false,
            'label'        => 'Firmantes'
        ] );
}

	public function configureOptions( OptionsResolver $resolver ) {
		$resolver->setDefaults( array(
			'data_class' => 'App\Entity\Dictamen'
		) );
	}

	public function getBlockPrefix() {
		return 'app_bundle_crear_dictamen_type';
	}

}