<?php

namespace App\Form;

use App\Form\BootstrapCollectionType;
use App\Entity\ProyectoBAE;
use App\Form\GiroType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ProyectoBAEGiroType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('giros',
                BootstrapCollectionType::class,
                [
                    'entry_type' => GiroType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => ProyectoBAE::class,
        ));
    }

    public function getBlockPrefix()
    {
        return 'app_bundle_proyecto_baegiro_type';
    }
}
