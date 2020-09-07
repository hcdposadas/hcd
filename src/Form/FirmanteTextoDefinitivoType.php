<?php

namespace App\Form;

use App\Entity\FirmanteTextoDefinitivo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

class FirmanteTextoDefinitivoType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
	        ->add( 'iniciador',
		        Select2EntityType::class,
		        [
			        'remote_route' => 'get_cargos_por_nombre',
			        'class'        => 'App\Entity\Iniciador',
			        'required'     => false,
			        'placeholder'  => 'Por Nombre',
			        'attr'         => [ 'class' => '' ],
			        'label'        => 'Concejal'

		        ] )
	        ->add( 'presidente' );
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => FirmanteTextoDefinitivo::class,
        ]);
    }
}
