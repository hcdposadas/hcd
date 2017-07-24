<?php

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use UtilBundle\Form\Type\Select2EntityType;

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
			        'class'        => 'AppBundle\Entity\Persona',
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
            'data_class' => 'AppBundle\Entity\PersonaACargo'
        ));
    }

    /**
     * {@inheritdoc}
     */
    public function getBlockPrefix()
    {
        return 'appbundle_personaacargo';
    }


}
