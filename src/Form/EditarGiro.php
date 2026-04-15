<?php

namespace App\Form;

use App\Entity\Expediente;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditarGiro extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('giros',
                BootstrapCollectionType::class,
                [
                    'entry_type' => GiroType::class,
                    'allow_add' => true,
                    'allow_delete' => true,
                    'by_reference' => false,
                ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Expediente::class,
        ]);
    }
}
