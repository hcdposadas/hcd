<?php

namespace AppBundle\Form;

use AppBundle\Entity\Mocion;
use AppBundle\Entity\Parametro;
use AppBundle\Entity\TipoMocion;
use AppBundle\Repository\ParametroRepository;
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

        $builder
            ->add('numero', IntegerType::class, array(
                'disabled' => true,
                'label' => 'Número',
                'data' => $siguienteNumero,
            ))
            ->add('tipoMayoria')
            ->add('expediente', EntityType::class, array(
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
