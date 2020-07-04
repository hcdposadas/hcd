<?php

namespace App\DataFixtures;

use App\Entity\Parametro;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ParametroFixtures extends Fixture {
	public function load( ObjectManager $manager ) {
		$parametros = [

			[
				'slug'  => 'mocion-estados-para-votar',
				'grupo' => 'mocion-estados',
				'valor' => 'Para votación',
				'orden' => 0
			],
			[
				'slug'  => 'mocion-estados-en-votacion',
				'grupo' => 'mocion-estados',
				'valor' => 'En votación',
				'orden' => 0
			],
			[
				'slug'  => 'mocion-estados-finalizado',
				'grupo' => 'mocion-estados',
				'valor' => 'Finalizado',
				'orden' => 0
			],
			[
				'slug'  => 'mocion-tipo-planificada',
				'grupo' => 'mocion-tipo',
				'valor' => 'Moción Planificada',
				'orden' => 0
			],
			[
				'slug'  => 'mocion-tipo-espontanea',
				'grupo' => 'mocion-tipo',
				'valor' => 'Moción Espontánea',
				'orden' => 0
			],
			[
				'slug'  => 'sesion-tipo-ordinaria',
				'grupo' => 'sesion-tipo',
				'valor' => 'Sesión Ordinaria',
				'orden' => 0
			],
			[
				'slug'  => 'sesion-tipo-extraordinaria',
				'grupo' => 'sesion-tipo',
				'valor' => 'Sesión Extraordinaria',
				'orden' => 0
			],
			[
				'slug'  => 'mail-concejales',
				'grupo' => 'mails',
				'valor' => 'concejales@hcdposadas.gob.ar',
				'orden' => 0
			],
			[
				'slug'  => 'mail-defensor',
				'grupo' => 'mails',
				'valor' => 'alberto.penayo@hcdposadas.gob.ar',
				'orden' => 0
			],
			[
				'slug'  => 'mail-secretaria',
				'grupo' => 'mails',
				'valor' => 'secretaria@hcdposadas.gob.ar',
				'orden' => 0
			],
			[ 'slug' => 'tiempo-mocion-extension', 'grupo' => 'tiempo-mocion', 'valor' => '5', 'orden' => 1 ],
			[ 'slug' => 'tiempo-mocion', 'grupo' => 'tiempo-mocion', 'valor' => '5', 'orden' => 0 ],
		];


		foreach ( $parametros as $item ) {
			$parametro = new Parametro();

			$parametro->setSlug( $item['slug'] );
			$parametro->setGrupo( $item['grupo'] );
			$parametro->setValor( $item['valor'] );
			$parametro->setOrden( $item['orden'] );
			$parametro->setActivo( true );
			$manager->persist( $parametro );
		}

		$manager->flush();
	}
}
