<?php

namespace App\Form;

use App\Entity\Comunicacion;
use App\Entity\AreaAdministrativa;
use App\Repository\AreaAdministrativaRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;


class ComunicacionType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('tipo', ChoiceType::class, [
                'choices' => [
                    'Nota' => 'NOTA',
                    'Memorándum' => 'MEMORANDUM',
                    'Circular' => 'CIRCULAR',
                    'Informe' => 'INFORME',

                ],
                'placeholder' => 'Selecciona el tipo', // Opcional, para mostrar un placeholder
            ])
            ->add('estado',
                TextareaType::class,
                ['label' => 'Extracto',
                    'attr' => ['rows' => 3, 'maxlength' => 250]
                ])
            ->add('archivoFile',
                VichFileType::class,
                [
                    'label' => 'Archivo Comunicado',
                    'required' => false,
                    'allow_delete' => true, // optional, default is true
                    'download_uri' => true, // optional, default is true
                ])
            ->add('archivos', CollectionType::class, [
                'entry_type' => ComunicacionArchivoType::class,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label' => 'Archivos Adjuntos',
            ])
            ->add('areaDestino', EntityType::class,
                [
                    'class' => AreaAdministrativa::class,
                    'query_builder' => function (AreaAdministrativaRepository $repository) {
                        return $repository->createQueryBuilder('a')
                            ->where('a.activo = true')
                            ->orderBy('a.nombre', 'ASC');
                    },
                    'multiple' => true,
                    'attr' => ['class' => 'select2', 'rows' => 3]
                ])
            ->add('masivo', ChoiceType::class, [
                'label' => 'Envio masivo',
                'mapped' => false,
                'required' => false,
                'choices' => [
                    'TODOS' => 'TODOS',
                    'CONCEJALES' => 'CONCEJALES',
                    'AREAS' => 'AREAS',
                    'COMISIONES' => 'COMISIONES'
                ],
                'placeholder' => 'Selecciona el tipo', // Opcional, para mostrar un placeholder
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comunicacion::class,
        ]);
    }
}
