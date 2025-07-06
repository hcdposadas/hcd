<?php

namespace App\Form;

use App\Entity\NoConformidad;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;

class TreatType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $noConformidad = $options['data'];
        $categoria = $noConformidad ? $noConformidad->getCategoria() : null;

        if ($categoria === 'no_conformidad') {
            // Para NO CONFORMIDAD - Solo análisis de causa, acción correctiva y fecha de acción correctiva
            $builder
                ->add('requiereAnalisisAccion', CheckboxType::class, [
                    'label' => 'Requiere ANÁLISIS DE CAUSA y ACCIÓN CORRECTIVA',
                    'mapped' => false,
                    'required' => false,
                    'attr' => ['class' => 'form-check-input', 'id' => 'requiere_analisis_accion']
                ])
                ->add('analisisCausa', TextareaType::class, [
                    'label' => 'ANÁLISIS DE CAUSA',
                    'attr' => [
                        'rows' => 4,
                        'placeholder' => 'Detalla análisis de causas'
                    ],
                    'required' => false
                ])
                ->add('accionCorrectiva', TextareaType::class, [
                    'label' => 'ACCIÓN CORRECTIVA',
                    'attr' => [
                        'rows' => 3,
                        'placeholder' => 'Campo de texto libre'
                    ],
                    'required' => false
                ])
                ->add('fechaAccionCorrectiva', DateType::class, [
                    'label' => 'FECHA DE ACCIÓN CORRECTIVA',
                    'widget' => 'single_text',
                    'required' => false,
                    'attr' => ['class' => 'form-control']
                ]);
        } else {
            // Para OBSERVACIÓN u OPORTUNIDAD DE MEJORA - Solo corrección y fecha de corrección
            $builder
                ->add('requiereCorreccionMejora', CheckboxType::class, [
                    'label' => 'Requiere una CORRECCIÓN Y/O ACCIÓN DE MEJORA',
                    'mapped' => false,
                    'required' => false,
                    'attr' => ['class' => 'form-check-input', 'id' => 'requiere_correccion_mejora']
                ])
                ->add('correccion', TextareaType::class, [
                    'label' => 'CORRECCIÓN Y/O ACCIÓN DE MEJORA',
                    'attr' => [
                        'rows' => 4,
                        'placeholder' => 'Campo de texto libre'
                    ],
                    'required' => false
                ])
                ->add('fechaCorreccion', DateType::class, [
                    'label' => 'FECHA DE LA CORRECCIÓN Y/O ACCIÓN DE MEJORA',
                    'widget' => 'single_text',
                    'required' => false,
                    'attr' => ['class' => 'form-control']
                ]);
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => NoConformidad::class,
        ]);
    }
} 