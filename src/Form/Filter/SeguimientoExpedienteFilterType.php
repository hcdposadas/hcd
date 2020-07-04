<?php

namespace App\Form\Filter;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Form\BootstrapCollectionType;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;
use Vich\UploaderBundle\Form\Type\VichFileType;

class SeguimientoExpedienteFilterType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tipoExpediente',
                null,
                [
                    'attr' => ['class' => 'tipo-expediente select2'],
                    'required' => false
                ])
            ->add('expediente',
                TextType::class,
                [
                    'label' => 'N° expediente',
//                    'required' => false
                ])
            ->add('anio',
                null,
                [
                    'label' => 'Año',
                    'required' => false
                ])
            ->add('letra', null, [
                'required' => false
            ])
            ->add('fecha',
                DateType::class,
                array(
                    'widget' => 'single_text',
                    'format' => 'dd/MM/yyyy',
                    'attr' => array(
                        'class' => 'datepicker',
                    ),
                    'required' => false
                ))
            ->add('registroMunicipal', null, [
                'required' => false
            ])
            ->add('areaAdministrativa', null, [
                'required' => false
            ])
            ->add('iniciador',
                Select2EntityType::class,
                [
                    'remote_route' => 'get_cargos_por_nombre',
                    'class' => 'App\Entity\Iniciador',
                    'required' => false,

                ])
            ->add('iniciadorParticular',
                Select2EntityType::class,
                [
                    'remote_route' => 'get_persona_por_dni',
                    'class' => 'App\Entity\Persona',
                    'required' => false,

                ]);
    }

    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
//            'required' => false
            //			'data_class' => 'App\Entity\Expediente'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'App_expediente_filter';
    }


}
