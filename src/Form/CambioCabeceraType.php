<?php

namespace App\Form;

use App\Entity\Comision;
use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\NotNull;

/**
 * Paso 2 del cambio de cabecera: elegir la comisión nueva (cualquier comisión activa).
 */
class CambioCabeceraType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder->add('comision', EntityType::class, [
            'class' => Comision::class,
            'label' => 'Nueva comisión de cabecera',
            'placeholder' => 'Seleccione una comisión',
            'attr' => ['class' => 'select2'],
            'constraints' => [new NotNull(['message' => 'Seleccione una comisión.'])],
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('c')
                    ->where('c.activo = :activo')
                    ->setParameter('activo', true)
                    ->orderBy('c.nombre', 'ASC');
            },
        ]);
    }
}
