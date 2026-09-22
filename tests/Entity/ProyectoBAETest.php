<?php

namespace App\Tests\Entity;

use App\Entity\Comision;
use App\Entity\Giro;
use App\Entity\ProyectoBAE;
use PHPUnit\Framework\TestCase;

class ProyectoBAETest extends TestCase
{
    public function testGiroCabeceraEsElGiroMarcado(): void
    {
        $proyectoBae = new ProyectoBAE();
        $this->assertNull($proyectoBae->getGiroCabecera());

        $comun = new Giro();
        $comun->setCabecera(false);
        $cabecera = new Giro();
        $cabecera->setCabecera(true);
        $proyectoBae->addGiro($comun);
        $proyectoBae->addGiro($cabecera);

        $this->assertSame($cabecera, $proyectoBae->getGiroCabecera());
    }

    public function testCamposDelCambioDeCabecera(): void
    {
        $proyectoBae = new ProyectoBAE();
        $this->assertNull($proyectoBae->getEsCambioCabecera());

        $anterior = new Comision();
        $agregado = new Giro();
        $proyectoBae->setEsCambioCabecera(true);
        $proyectoBae->setComisionCabeceraAnterior($anterior);
        $proyectoBae->setGiroCabeceraAgregado($agregado);

        $this->assertTrue($proyectoBae->getEsCambioCabecera());
        $this->assertSame($anterior, $proyectoBae->getComisionCabeceraAnterior());
        $this->assertSame($agregado, $proyectoBae->getGiroCabeceraAgregado());
    }

    public function testTituloDeLaHoja(): void
    {
        $proyectoBae = new ProyectoBAE();
        $this->assertSame('PASE A COMISIÓN', $proyectoBae->getTituloHojaGiro());

        $proyectoBae->setEsCambioCabecera(true);
        $this->assertSame('CAMBIO DE CABECERA', $proyectoBae->getTituloHojaGiro());
    }
}
