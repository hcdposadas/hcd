<?php

namespace App\Form;

use App\Entity\TipoMayoria;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TipoMayoriaType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $funciones = array();
        foreach (TipoMayoria::funciones() as $f) {
            $funciones[$f] = $f;
        }

        $builder
            ->add('nombre')
            ->add('funcion', ChoiceType::class, array(
                'choices' => $funciones,
            ))
        ;
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => TipoMayoria::class
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'App_tipomayoria';
    }
}
