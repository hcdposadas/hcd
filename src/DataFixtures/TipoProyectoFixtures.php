<?php

namespace App\DataFixtures;

use App\Entity\TipoProyecto;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class TipoProyectoFixtures extends Fixture {
	public function load( ObjectManager $manager ) {

		$ciudad = $_ENV['CIUDAD_NAME'];

		$tipoProyecto = new TipoProyecto();
		$tipoProyecto->setSlug( 'comunicacion' );
		$tipoProyecto->setNombre( 'Comunicación' );
		$tipoProyecto->setTextoPorDefecto( '<p style="text-align:center"><u><strong>PROYECTO DE COMUNICACI&Oacute;N</strong></u></p>

<p style="text-align:center">EL HONORABLE CONCEJO DELIBERANTE DE LA CIUDAD DE ' . $ciudad . '&nbsp;</p>

<p style="text-align:center"><u><strong>COMUNICA</strong></u></p>

<p style="text-align:start"><u><strong>ART&Iacute;CULO 1&ordm;</strong></u>.-&nbsp;</p>

<p style="text-align:start">&nbsp;</p>

<p style="text-align:center"><u><strong>FUNDAMENTOS</strong></u></p>' );
		$manager->persist( $tipoProyecto );

		$tipoProyecto = new TipoProyecto();
		$tipoProyecto->setNombre( 'Ordenanza' );
		$tipoProyecto->setSlug( 'ordenanza' );
		$tipoProyecto->setTextoPorDefecto( '<p style="text-align:center"><u><strong>PROYECTO DE ORDENANZA</strong></u></p>

<p style="text-align:center">EL HONORABLE CONCEJO DELIBERANTE DE LA CIUDAD DE ' . $ciudad . '&nbsp;</p>

<p style="text-align:center">SANCIONA CON FUERZA DE</p>

<p style="text-align:center"><u><strong>ORDENANZA</strong></u></p>

<p style="text-align:start"><u><strong>ART&Iacute;CULO 1&ordm;</strong></u>.-&nbsp;</p>

<p style="text-align:start">&nbsp;</p>

<p style="text-align:center"><u><strong>FUNDAMENTOS</strong></u></p>' );
		$manager->persist( $tipoProyecto );

		$tipoProyecto = new TipoProyecto();
		$tipoProyecto->setNombre( 'Resolución' );
		$tipoProyecto->setSlug( 'resolucion' );
		$tipoProyecto->setTextoPorDefecto( '<p style="text-align:center"><u><strong>PROYECTO DE RESOLUCI&Oacute;N</strong></u></p>

<p style="text-align:center">EL HONORABLE CONCEJO DELIBERANTE DE LA CIUDAD DE ' . $ciudad . '&nbsp;</p>

<p style="text-align:center"><u><strong>RESUELVE</strong></u></p>

<p style="text-align:start"><u><strong>ART&Iacute;CULO 1&ordm;</strong></u>.-&nbsp;</p>

<p style="text-align:start">&nbsp;</p>

<p style="text-align:center"><u><strong>FUNDAMENTOS</strong></u></p>' );
		$manager->persist( $tipoProyecto );

		$tipoProyecto = new TipoProyecto();
		$tipoProyecto->setNombre( 'Declaración' );
		$tipoProyecto->setSlug( 'declaracion' );
		$tipoProyecto->setTextoPorDefecto( '<p style="text-align:center"><u><strong>PROYECTO DE DECLARACI&Oacute;N</strong></u></p>

<p style="text-align:center">EL HONORABLE CONCEJO DELIBERANTE DE LA CIUDAD DE ' . $ciudad . '&nbsp;</p>

<p style="text-align:center"><u><strong>DECLARA</strong></u></p>

<p style="text-align:start"><u><strong>ART&Iacute;CULO 1&ordm;</strong></u>.-&nbsp;</p>

<p style="text-align:start">&nbsp;</p>

<p style="text-align:center"><u><strong>FUNDAMENTOS</strong></u></p>' );
		$manager->persist( $tipoProyecto );

		$manager->flush();
	}
}
