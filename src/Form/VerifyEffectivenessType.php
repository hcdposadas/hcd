<?php

namespace App\Form;

use App\Entity\NoConformidad;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class VerifyEffectivenessType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $noConformidad = $options['data'];
        $categoria = $noConformidad ? $noConformidad->getCategoria() : null;
        
        // Este formulario es específico para No Conformidades
        // pero mantenemos consistencia con la verificación por categoría
        
        $builder
            ->add('efectividadVerificada', ChoiceType::class, [
                'label' => '¿La acción correctiva ha sido efectiva?',
                'choices' => [
                    'Sí' => true,
                    'No' => false
                ],
                'placeholder' => 'Seleccione una opción',
                'required' => true,
                'expanded' => true,
                'attr' => ['class' => 'efectividad-radio']
            ])
            ->add('comentariosVerificacion', TextareaType::class, [
                'label' => 'Comentarios de Verificación',
                'attr' => ['rows' => 4],
                'required' => false
            ])
            ->add('nuevaFechaEfectividad', DateType::class, [
                'label' => 'Nueva Fecha de Verificación de Efectividad',
                'widget' => 'single_text',
                'required' => false,
                'mapped' => false,
                'attr' => [
                    'min' => date('Y-m-d', strtotime('+1 day'))
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => NoConformidad::class,
        ]);
    }
} 