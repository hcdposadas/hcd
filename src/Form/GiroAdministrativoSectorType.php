<?php

namespace App\Form;

use App\Entity\GiroAdministrativo;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Vich\UploaderBundle\Form\Type\VichFileType;


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
            ->add('anexoFile',VichFileType::class,
            [
                'label'        => 'Anexo Giro',
                'required'     => false,
                'allow_delete' => true, // optional, default is true
                'download_uri' => true, // optional, default is true
            ] );

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
