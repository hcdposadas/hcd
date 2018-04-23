<?php

namespace MesaEntradaBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use UtilBundle\Form\Type\Select2EntityType;

class IniciadorExpedienteType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
//            ->add('expediente')
//            ->add('iniciador')
            ->add('iniciador',
                Select2EntityType::class,
                [
                    'remote_route' => 'get_cargos_por_nombre',
                    'class' => 'MesaEntradaBundle\Entity\Iniciador',
                    'required' => false,
                    'placeholder' => 'Por Nombre',
                    'attr'=> ['class'=>''],
	                'label'=> 'Concejal'

                ])
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'MesaEntradaBundle\Entity\IniciadorExpediente'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'mesaentradabundle_iniciadorexpediente';
    }


}
