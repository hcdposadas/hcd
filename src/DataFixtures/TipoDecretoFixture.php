<?php

namespace App\DataFixtures;

use App\Entity\TipoDecreto;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TipoDecretoFixture extends Fixture {
	public function load( ObjectManager $manager ) {
		$tiposDecretos = [
			'Contratación',
			'Pago de Servicios',
			'Beca',
			'Licitación',
		];

		foreach ( $tiposDecretos as $itemTiposDecreto ) {
			$tipoDecreto = new TipoDecreto();
			$tipoDecreto->setNombre( $itemTiposDecreto );
			$manager->persist($tipoDecreto);
		}

		$manager->flush();
	}
}
