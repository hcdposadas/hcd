<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Tetranz\Select2EntityBundle\Form\Type\Select2EntityType;

class PersonaACargoType extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

	        ->add('tipoRelacionPersona', null, [
	        	'label'=> 'Tipo Relación'
	        ])
//	        ->add('personaACargo')
	        ->add( 'personaACargo',
		        Select2EntityType::class,
		        [
			        'remote_route' => 'get_persona_por_dni',
			        'class'        => 'App\Entity\Persona',
			        'required'     => false,
			        'placeholder'=> 'Por DNI'

		        ] )
        ;
    }
    
    /**
     * {@inheritdoc}
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'App\Entity\PersonaACargo'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'App_personaacargo';
    }


}
