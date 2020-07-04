<?php

namespace App\Form;

use App\Entity\Usuario;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PerfilUsuarioType extends AbstractType {
	public function buildForm( FormBuilderInterface $builder, array $options ) {
		$builder
			->add( 'email' )
			->add( 'passwordPlano',
				RepeatedType::class,
				[
					'type'            => PasswordType::class,
					'invalid_message' => 'Los password no coinciden',
					'options'         => [ 'attr' => [ 'class' => 'password-field' ] ],
					'required'        => true,
					'mapped'          => false,
					'first_options'   => [ 'label' => 'Contraseña' ],
					'second_options'  => [ 'label' => 'Repertir Contraseña' ],
				] );;
	}

	public function configureOptions( OptionsResolver $resolver ) {
		$resolver->setDefaults( [
			'data_class' => Usuario::class,
		] );
	}
}
