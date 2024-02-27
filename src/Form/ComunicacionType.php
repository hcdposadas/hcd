<?php

namespace App\Form;

use App\Entity\Comunicacion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;
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
            ],
            'placeholder' => 'Selecciona el tipo', // Opcional, para mostrar un placeholder
        ])
            ->add( 'estado',
            TextareaType::class,
            [   'label' =>'Extracto',
                'attr' => [ 'rows' => 3 ]
            ] )
            ->add( 'archivoFile',
            VichFileType::class,
            [
                'label'        => 'Archivo Comunicado',
                'required'     => true,
                'allow_delete' => true, // optional, default is true
                'download_uri' => true, // optional, default is true
            ] )
            ->add('areaDestino',null,
            [
                'attr' => [ 'class' => 'select2',  'rows' => 3 ]
            ] )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comunicacion::class,
        ]);
    }
}
