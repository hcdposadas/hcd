<?php

namespace AppBundle\Form;

use AppBundle\Entity\Mocion;
use AppBundle\Entity\Parametro;
use AppBundle\Entity\Sesion;
use Doctrine\ORM\EntityManagerInterface;
use MesaEntradaBundle\Entity\Expediente;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class MocionType extends AbstractType
{
    /**
     * @var EntityManagerInterface
     */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $siguienteNumero = $this->em->getRepository(Mocion::class)->siguienteNumero();

        $sesion = null;

        $builder
            ->add('sesion', EntityType::class, array(
                'label' => 'Sesión',
                'class' => Sesion::class,
                'data' => $sesion,
            ))
            ->add('numero', IntegerType::class, array(
                'disabled' => true,
                'label' => 'Número',
                'data' => $siguienteNumero,
            ))
            ->add('tipo', EntityType::class, array(
                'label' => 'Tipo de moción',
                'class' => Parametro::class,
                'query_builder' => $this->em->getRepository(Parametro::class)->grupo('mocion-tipo'),
            ))
            ->add('tipoMayoria')
            ->add('expediente', EntityType::class, array(
                'required' => false,
                'label' => 'Expediente',
                'class' => Expediente::class,
            ))
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'AppBundle\Entity\Mocion'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_mocion';
    }


}
