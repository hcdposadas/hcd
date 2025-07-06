<?php

namespace App\Form;

use App\Entity\NoConformidad;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class VerifyCorrectionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $noConformidad = $options['data'];
        $categoria = $noConformidad ? $noConformidad->getCategoria() : null;
        
        if ($categoria === 'no_conformidad') {
            // Para NO CONFORMIDAD - verificar acción correctiva
            $label = 'Verificado Realización de la Acción Correctiva';
            $fechaLabel = 'Nueva Fecha de Acción Correctiva';
        } else {
            // Para OBSERVACIÓN/OPORTUNIDAD DE MEJORA - verificar corrección
            $label = 'Verificado Realización de la Corrección/Acción de Mejora';
            $fechaLabel = 'Nueva Fecha de Corrección/Acción de Mejora';
        }
        
        $builder
            ->add('correccionVerificada', ChoiceType::class, [
                'label' => $label,
                'choices' => [
                    'SÍ' => true,
                    'NO' => false
                ],
                'expanded' => true,
                'multiple' => false,
                'placeholder' => false,
                'required' => true,
                'attr' => ['class' => 'form-check-input']
            ])
            ->add('comentariosVerificacion', TextareaType::class, [
                'label' => 'Comentarios',
                'attr' => [
                    'rows' => 4,
                    'placeholder' => 'Campo de texto libre para comentarios'
                ],
                'required' => true
            ])
            ->add('nuevaFechaCorreccion', DateType::class, [
                'label' => $fechaLabel,
                'widget' => 'single_text',
                'required' => false,
                'attr' => [
                    'class' => 'form-control'
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