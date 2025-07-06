<?php

namespace App\Form;

use App\Entity\NoConformidad;
use App\Entity\AreaAdministrativa;
use App\Entity\Persona;
use App\Form\BootstrapCollectionType;
use App\Form\NoConformidadAdjuntoType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Doctrine\ORM\EntityRepository;

class NoConformidadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('area', EntityType::class, [
                'class' => AreaAdministrativa::class,
                'label' => 'Área',
                'attr' => ['class' => 'select2'],
                'placeholder' => 'Seleccione un área'
            ])
            ->add('empleado', EntityType::class, [
                'class' => Persona::class,
                'label' => 'Empleado',
                'attr' => ['class' => 'select2'],
                'placeholder' => 'Seleccione un empleado',
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('p')
                        ->orderBy('p.apellido', 'ASC')
                        ->addOrderBy('p.nombre', 'ASC');
                },
                'choice_label' => function ($persona) {
                    return $persona->getApellido() . ', ' . $persona->getNombre();
                },
            ])
            ->add('origen', ChoiceType::class, [
                'label' => 'Origen',
                'choices' => [
                    'Auditoría Interna' => 'auditoria_interna',
                    'Auditoría Externa' => 'auditoria_externa',
                    'Reclamos' => 'reclamos',
                    'Espontánea' => 'espontanea',
                    'FODA' => 'foda'
                ],
                'placeholder' => 'Seleccione el origen'
            ])
            ->add('categoria', ChoiceType::class, [
                'label' => 'Categoría',
                'choices' => [
                    'No Conformidad' => 'no_conformidad',
                    'Observación' => 'observacion',
                    'Oportunidad de Mejora' => 'oportunidad_mejora'
                ],
                'placeholder' => 'Seleccione la categoría'
            ])
            ->add('descripcionHallazgo', TextareaType::class, [
                'label' => 'Descripción del Hallazgo',
                'attr' => ['rows' => 4, 'placeholder' => 'Describa detalladamente el hallazgo encontrado'],
                'required' => true
            ])
            ->add('norma', ChoiceType::class, [
                'label' => 'Norma',
                'choices' => [
                    '9001' => '9001',
                    '14001' => '14001',
                    '45001' => '45001',
                    '27001' => '27001'
                ],
                'placeholder' => 'Seleccionar norma',
                'required' => false
            ])
            ->add('requisitoEspecifico', ChoiceType::class, [
                'label' => 'Requisito Específico',
                'choices' => [
                    '4.1' => '4.1',
                    'Otros' => 'otros'
                ],
                'placeholder' => 'Seleccione un requisito',
                'required' => false
            ])
            ->add('pgcd', ChoiceType::class, [
                'label' => 'PGCD',
                'choices' => [
                    'Procedimiento Control de Documentos' => 'proc_control_documentos',
                    'Procedimiento Auditorías Internas' => 'proc_auditorias_internas',
                    'Procedimiento Revisión por la Dirección' => 'proc_revision_direccion',
                    'Procedimiento Control de Registros' => 'proc_control_registros',
                    'Procedimiento Acciones Correctivas' => 'proc_acciones_correctivas',
                    'Procedimiento Control de No Conformidades' => 'proc_control_no_conformidades',
                    'Procedimiento Mejora Continua' => 'proc_mejora_continua',
                    'Procedimiento Capacitación' => 'proc_capacitacion',
                    'Procedimiento Comunicaciones' => 'proc_comunicaciones',
                    'Procedimiento Gestión de Riesgos' => 'proc_gestion_riesgos'
                ],
                'placeholder' => 'Seleccionar Procedimiento',
                'required' => false
            ])
            ->add('requisitoLegal', ChoiceType::class, [
                'label' => 'Requisito Legal',
                'choices' => [
                    'Sí' => true,
                    'No' => false
                ],
                'placeholder' => 'Seleccionar',
                'required' => false,
                'expanded' => false
            ])
            ->add('requisito', TextareaType::class, [
                'label' => 'Requisito',
                'attr' => ['rows' => 3],
                'required' => false
            ])
            ->add('adjuntos', BootstrapCollectionType::class, [
                'entry_type' => NoConformidadAdjuntoType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'Archivos Adjuntos',
                'attr' => [
                    'class' => 'collection-widget'
                ]
            ])
        ;

        // Solo agregar estos campos si estamos editando
        if ($options['is_edit']) {
            $builder
                ->add('estado', ChoiceType::class, [
                    'label' => 'Estado',
                    'choices' => [
                        'Nuevo' => 'nuevo',
                        'Revisado' => 'revisado',
                        'Tratado' => 'tratado',
                        'Aceptado' => 'aceptado',
                        'Verificada Corrección' => 'verificada_correccion',
                        'Verificada Efectividad' => 'verificada_efectividad',
                        'Desestimado' => 'desestimado'
                    ],
                    'disabled' => true,
                    'attr' => ['readonly' => true]
                ])
                // ->add('responsableCalidad', ChoiceType::class, [
                //     'label' => 'Responsable de Calidad',
                //     'choices' => [
                //         'Juan Pérez' => 'Juan Pérez',
                //         'María García' => 'María García',
                //         'Carlos López' => 'Carlos López',
                //         'Ana Martínez' => 'Ana Martínez'
                //     ],
                //     'placeholder' => 'Seleccione un responsable',
                //     'required' => false
                // ])
            ;
        }
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => NoConformidad::class,
            'is_edit' => false
        ]);
    }
} 