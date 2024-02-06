<?php

namespace App\Form;

use App\Entity\Ticket;
use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\AreaAdministrativa;
use App\Repository\AreaAdministrativaRepository; // Añade esta línea
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\OptionsResolver\OptionsResolver;

class TicketType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('texto',
            TextareaType::class,
            [
                'attr' => [ 'rows' => 3, 'maxlength' => 250  ],
                'label' => 'Descripción'
            ] )
            ->add('areaDestino', EntityType::class, [
                'class' => AreaAdministrativa::class,
                'query_builder' => function (AreaAdministrativaRepository $repository) {
                    $ids = [40, 32, 25, 21, 34]; // Tus 5 IDs específicos
                    return $repository->createQueryBuilder('a')
                        ->where('a.id IN (:ids)')
                        ->setParameter('ids', $ids);
                },'label' => 'Área De Destino'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Ticket::class,
        ]);
    }
}
