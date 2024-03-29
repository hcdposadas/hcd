<?php

namespace App\Form;

use App\Entity\Paciente;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;

class PacienteType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('grupo', ChoiceType::class, [
                'choices'  => [
                    'A' => 'A',
                    'B' => 'B',
                    'AB' => 'AB',
                    'O' => 'O',
                ],
            ])
            ->add('factor', ChoiceType::class, [
                'choices'  => [
                    'Rh+' => 'Rh+',
                    'Rh-' => 'Rh-',
                ],
            ])
            ->add('observaciones', TextareaType::class)
            ->add('foto', FileType::class)
            ->add('guardar', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}
