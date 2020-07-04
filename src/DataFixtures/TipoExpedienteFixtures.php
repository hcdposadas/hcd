<?php

namespace App\DataFixtures;

use App\Entity\TipoExpediente;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TipoExpedienteFixtures extends Fixture {
	public function load( ObjectManager $manager ) {
		$tipoExpediente = new TipoExpediente();
		$tipoExpediente->setNombre( 'Externo' );
		$tipoExpediente->setDescripcion( 'Legislativo' );
		$manager->persist( $tipoExpediente );

		$tipoExpediente = new TipoExpediente();
		$tipoExpediente->setNombre( 'Interno' );
		$tipoExpediente->setDescripcion( 'Administrativo' );
		$manager->persist( $tipoExpediente );

		$manager->flush();
	}
}
