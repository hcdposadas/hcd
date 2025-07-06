<?php

namespace App\Form;

use App\Entity\AreaAdministrativa;
use App\Entity\Persona;
use App\Entity\Usuario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Doctrine\ORM\EntityRepository;

class NoConformidadFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        // Generar opciones de años (últimos 5 años hasta el actual)
        $currentYear = date('Y');
        $years = [];
        for ($i = $currentYear; $i >= $currentYear - 5; $i--) {
            $years[$i] = $i;
        }
        
        // Meses del año
        $months = [
            'Enero' => '01',
            'Febrero' => '02',
            'Marzo' => '03',
            'Abril' => '04',
            'Mayo' => '05',
            'Junio' => '06',
            'Julio' => '07',
            'Agosto' => '08',
            'Septiembre' => '09',
            'Octubre' => '10',
            'Noviembre' => '11',
            'Diciembre' => '12'
        ];
        
        $builder
            ->add('mesDesde', ChoiceType::class, [
                'label' => 'Mes Desde',
                'choices' => $months,
                'placeholder' => 'Seleccione mes',
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('anoDesde', ChoiceType::class, [
                'label' => 'Año Desde',
                'choices' => $years,
                'placeholder' => 'Seleccione año',
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('mesHasta', ChoiceType::class, [
                'label' => 'Mes Hasta',
                'choices' => $months,
                'placeholder' => 'Seleccione mes',
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('anoHasta', ChoiceType::class, [
                'label' => 'Año Hasta',
                'choices' => $years,
                'placeholder' => 'Seleccione año',
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('area', EntityType::class, [
                'class' => AreaAdministrativa::class,
                'label' => 'Área',
                'attr' => ['class' => 'select2 form-control'],
                'placeholder' => 'Todas las áreas',
                'required' => false,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('a')
                        ->where('a.activo = true')
                        ->orderBy('a.nombre', 'ASC');
                },
                'choice_label' => function($area) {
                    return $area->getNombre();
                },
                'choice_attr' => function($area) {
                    // Agregar atributos data para ordenamiento personalizado en el frontend
                    $priority = 999; // Por defecto, prioridad baja
                    if (stripos($area->getNombre(), 'Presidencia') !== false) {
                        $priority = 1;
                    } elseif (stripos($area->getNombre(), 'Prosecretaria Legislativa') !== false) {
                        $priority = 2;
                    }
                    return ['data-priority' => $priority];
                }
            ])
            ->add('usuario', EntityType::class, [
                'class' => Usuario::class,
                'label' => 'Usuario Responsable del Área',
                'attr' => ['class' => 'select2 form-control'],
                'placeholder' => 'Todos los usuarios',
                'required' => false,
                'query_builder' => function (EntityRepository $er) {
                    return $er->createQueryBuilder('u')
                        ->leftJoin('u.persona', 'p')
                        ->where('u.enabled = true')
                        ->orderBy('p.apellido', 'ASC')
                        ->addOrderBy('p.nombre', 'ASC');
                },
                'choice_label' => function($usuario) {
                    if ($usuario->getPersona()) {
                        return $usuario->getPersona()->getApellido() . ', ' . $usuario->getPersona()->getNombre();
                    }
                    return $usuario->getEmail();
                }
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
                'placeholder' => 'Todos los orígenes',
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('categoria', ChoiceType::class, [
                'label' => 'Categoría Hallazgo',
                'choices' => [
                    'No Conformidad' => 'no_conformidad',
                    'Observación' => 'observacion',
                    'Oportunidad de Mejora' => 'oportunidad_mejora'
                ],
                'placeholder' => 'Todas las categorías',
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('norma', ChoiceType::class, [
                'label' => 'Norma',
                'choices' => [
                    'ISO 9001' => '9001',
                    'ISO 14001' => '14001',
                    'ISO 45001' => '45001',
                    'ISO 27001' => '27001'
                ],
                'placeholder' => 'Todas las normas',
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('requisitoEspecifico', ChoiceType::class, [
                'label' => 'Requisito',
                'choices' => [
                    '4.1' => '4.1',
                    'Otros' => 'otros'
                ],
                'placeholder' => 'Todos los requisitos',
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
            ->add('pgcd', ChoiceType::class, [
                'label' => 'PGCD (Procedimiento)',
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
                'attr' => ['class' => 'select2 form-control'],
                'placeholder' => 'Todos los procedimientos',
                'required' => false
            ])
            ->add('estado', ChoiceType::class, [
                'label' => 'Estado',
                'choices' => [
                    'Nuevo' => 'nuevo',
                    'Revisado' => 'revisado',
                    'Tratado' => 'tratado',
                    'Aceptado' => 'aceptado',
                    'Verificada Corrección/Acción de Mejora' => 'verificada_correccion',
                    'Cerrado' => 'verificada_efectividad',
                    'Desestimado' => 'desestimado'
                ],
                'placeholder' => 'Todos los estados',
                'required' => false,
                'attr' => ['class' => 'form-control']
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'method' => 'GET',
            'csrf_protection' => false,
        ]);
    }
} 