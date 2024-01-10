<?php

namespace App\Form;

use App\Entity\GiroAdministrativo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class GiroAdministrativoSectorType extends AbstractType
{
    	/**
	 * {@inheritdoc}
	 */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('areaDestino',null,
            [
                'attr' => [ 'class' => 'select2' ]
            ] )

        ;
    }

    	/**
	 * {@inheritdoc}
	 */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => GiroAdministrativo::class,
        ]);
    }

    	/**
	 * {@inheritdoc}
	 */
	public function getBlockPrefix() {
		return 'App_giroadministrativo';
	}
}
