<?php

namespace App\Form;

use App\Entity\OrdenMedica;
use App\Entity\PersonalArticulo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;

class OrdenType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('medicoOtorgante', TextType::class)
            ->add('desde', DateType::class, ['widget' => 'single_text'])
            ->add('hasta', DateType::class, ['widget' => 'single_text'])
            ->add('articulo', EntityType::class, ['class' => PersonalArticulo::class])
            ->add('diagnostico', FileType::class)
            ->add('guardar', SubmitType::class);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([]);
    }
}
