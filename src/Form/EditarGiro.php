<?php

namespace App\Form;

use App\Entity\Expediente;
use App\Entity\Sesion;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class EditarGiro extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('sesion',
                EntityType::class,
                [
                    'class' => Sesion::class,
                    'mapped' => false,
                    'required' => false,
                    'placeholder' => 'No incorporar en sesión',
                    'label' => 'Sesión',
                    'attr' => [ 'class' => 'select2' ],
                    'choice_label' => 'tituloLargo',
                    'query_builder' => function (EntityRepository $er) {
                        return $er->createQueryBuilder('s')
                            ->distinct()
                            ->innerJoin('s.bae', 'bae')
                            ->orderBy('s.fecha', 'DESC')
                            ->addOrderBy('s.id', 'DESC');
                    },
                ])
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
