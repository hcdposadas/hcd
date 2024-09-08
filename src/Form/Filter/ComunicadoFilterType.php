<?php

namespace App\Form\Filter;

use App\Entity\Comunicacion;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Doctrine\ORM\EntityRepository;

class ComunicadoFilterType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('numero')
            ->add('fecha',				DateType::class,
			array(
				'widget' => 'single_text',
				'html5'  => true,
                'required' => false
			) )
            ->add('tipo')
            ->add('areaOrigen',null,
            ['attr' => [ 'class' => 'select2',  'rows' => 12 , 'style' => 'width: 30%;' ],
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('ao')
                ->orderBy('ao.nombre', 'ASC');
            }])
            ->add('areaDestino',null,
            ['attr' => [ 'class' => 'select2',  'rows' => 12 , 'style' => 'width: 30%;' ],
            'query_builder' => function (EntityRepository $er) {
                return $er->createQueryBuilder('ao')
                ->orderBy('ao.nombre', 'ASC');
            }])
            ->add('estado',TextType::class,[
				'label' => 'Extracto',
				'required' => false
			])
            ->add( 'buscar',
            SubmitType::class,
            array(
                'attr' => [ 'class' => 'btn btn-primary' ],
            ) )
        ->add( 'limpiar',
            ResetType::class,
            array(
                'attr' => [ 'class' => 'btn btn-default reset' ],
            ) )
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => Comunicacion::class,
        ]);
    }
}
