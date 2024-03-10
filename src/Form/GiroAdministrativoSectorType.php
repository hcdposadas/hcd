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
            ->add('anexoGiros',
            BootstrapCollectionType::class,
            [
                'entry_type'   => AnexoGiroType::class,
                'allow_add'    => true,
                'allow_delete' => true,
                'by_reference' => false,
                'label'        => 'Anexos'
            ] )
            ->add('areaDestino',null,
            [
                'attr' => [ 'class' => 'select2' ]
            ] )
            ->add( 'texto' );


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
