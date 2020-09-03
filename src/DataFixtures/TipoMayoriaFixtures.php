<?php

namespace App\DataFixtures;

use App\Entity\TipoMayoria;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TipoMayoriaFixtures extends Fixture {
	public function load( ObjectManager $manager ) {

		$tipoMayoria = new TipoMayoria();
		$tipoMayoria->setNombre('Mayoría Simple');
		$tipoMayoria->setSlug('mayoriaSimple');
		$tipoMayoria->setCantidades('{"14":8,"13":7,"12":7,"11":6,"10":6,"9":5,"8":5}');
		$manager->persist( $tipoMayoria );

		$tipoMayoria = new TipoMayoria();
		$tipoMayoria->setNombre('Mayoría Calificada');
		$tipoMayoria->setSlug('mayoriaCalificada');
		$tipoMayoria->setCantidades('{"cantidad": 10}');
		$manager->persist( $tipoMayoria );

		$tipoMayoria = new TipoMayoria();
		$tipoMayoria->setNombre('Mayoría Calificada Presentes');
		$tipoMayoria->setSlug('mayoriaCalificadaPresentes');
		$tipoMayoria->setCantidades('{"14":10,"13":9,"12":8,"11":8,"10":7,"9":6,"8":6}');
		$manager->persist( $tipoMayoria );

		$manager->flush();
	}
}
