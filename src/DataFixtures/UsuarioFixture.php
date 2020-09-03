<?php

namespace App\DataFixtures;

use App\Entity\Usuario;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class UsuarioFixture extends Fixture {

	private $passwordEncoder;

	public function __construct( UserPasswordEncoderInterface $passwordEncoder ) {
		$this->passwordEncoder = $passwordEncoder;
	}

	public function load( ObjectManager $manager ) {
		$user = new Usuario();
		$user->setUsername( 'admin' );
		$user->setEmail( 'matias.delatorre89@gmail.com' );
		$user->setEnabled( true );
		$user->setPassword( $this->passwordEncoder->encodePassword(
			$user,
			'hcd123'
		) );

		$user->setRoles( [ 'ROLE_ADMIN' ] );

		$manager->persist($user);

		$manager->flush();
	}
}
