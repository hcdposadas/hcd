<?php

namespace App\DataFixtures;

use App\Entity\Configuracion;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ConfiguracionFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        // $product = new Product();
        // $manager->persist($product);

	    $configuracion = new Configuracion();



        $manager->flush();
    }
}
