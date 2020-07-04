<?php

namespace App\DataFixtures;

use App\Entity\Cargo;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class CargoFixtures extends Fixture {
	public function load( ObjectManager $manager ) {
		// $product = new Product();
		// $manager->persist($product);
		$cargos = [
			'Jefe',
			'Prosecretario',
			'Secretario',
			'Vicepresidente 1°',
			'Presidente',
			'Concejal',
			'Defensor del Pueblo',
			'Director',
			'Particular',
			'Vicepresidente 1º A/C de la Presidencia',
			'Vicepresidente 2° A/C de la Presidencia',
			'Vicepresidente 2°',
		];

		foreach ( $cargos as $itemCargo ) {
			$cargo = new Cargo();
			$cargo->setNombre( $itemCargo );
			$manager->persist( $cargo );
		}

		$manager->flush();
	}
}
