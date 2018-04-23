<?php

namespace MesaEntradaBundle\Form;

use Doctrine\ORM\EntityRepository;
use Ivory\CKEditorBundle\Form\Type\CKEditorType;
use MesaEntradaBundle\Entity\Expediente;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use UsuariosBundle\Entity\Usuario;

class EditarExtractoType extends AbstractType {
	/**
	 * {@inheritdoc}
	 */
	public function buildForm( FormBuilderInterface $builder, array $options ) {
		$builder
            ->add( 'extractoDictamen',
                CKEditorType::class,
                [
                    'required' => true,
                    'config'   => array(
                        'uiColor' => '#ffffff',
                    ),
                    'attr'     => [ 'class' => 'texto_por_defecto' ]
                ] )
            ->add( 'extractoTemario',
                CKEditorType::class,
                [
                    'required' => true,
                    'config'   => array(
                        'uiColor' => '#ffffff',
                    ),
                    'attr'     => [ 'class' => 'texto_por_defecto' ]
                ] )
			;
	}

	/**
	 * {@inheritdoc}
	 */
	public function configureOptions( OptionsResolver $resolver ) {
		$resolver->setDefaults( array(
			'data_class' => Expediente::class,
		) );
	}

	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix() {
		return 'appbundle_editarExtracto';
	}


}
