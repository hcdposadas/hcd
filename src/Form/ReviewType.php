<?php

namespace App\Form;

use App\Entity\NoConformidad;
use App\Entity\Usuario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use App\Entity\AreaAdministrativa;

class ReviewType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('accionHallazgo', ChoiceType::class, [
                'label' => 'Acción sobre el Hallazgo',
                'choices' => [
                    'Aceptar Hallazgo' => 'aceptar',
                    'Desestimar Hallazgo' => 'desestimar'
                ],
                'mapped' => false,
                'required' => true,
                'placeholder' => 'Seleccione una acción',
                'attr' => ['class' => 'select2']
            ])
            ->add('asignadoA', EntityType::class, [
                'class' => Usuario::class,
                'label' => 'Asignado A (usuario)',
                'placeholder' => 'Seleccione un responsable',
                'required' => false,
                'choice_label' => function(Usuario $usuario) {
                    return $usuario->getPersona() ? $usuario->getPersona()->getNombreDisplay() : $usuario->getUsername();
                },
                'attr' => ['class' => 'select2']
            ])
            ->add('areaAsignada', EntityType::class, [
                'class' => AreaAdministrativa::class,
                'label' => 'Área',
                'placeholder' => 'Seleccione un área',
                'required' => false,
                'mapped' => false,
                'attr' => ['class' => 'select2']
            ])
            ->add('explicacion', TextareaType::class, [
                'label' => 'Explicación (solo en caso de desestimar)',
                'mapped' => false,
                'required' => false,
                'attr' => [
                    'rows' => 4,
                    'placeholder' => 'Ingrese la explicación del motivo por el cual se desestima el hallazgo'
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