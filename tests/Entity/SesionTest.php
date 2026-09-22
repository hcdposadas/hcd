<?php

namespace App\Tests\Entity;

use App\Entity\Parametro;
use App\Entity\Sesion;
use PHPUnit\Framework\TestCase;

class SesionTest extends TestCase
{
    public function testSinTipoNoEsOrdinaria(): void
    {
        $this->assertFalse((new Sesion())->esOrdinaria());
    }

    public function testEsOrdinariaSegunElSlugDelTipo(): void
    {
        $tipo = new Parametro();
        $tipo->setSlug(Sesion::SLUG_TIPO_ORDINARIA);
        $sesion = new Sesion();
        $sesion->setTipoSesion($tipo);

        $this->assertTrue($sesion->esOrdinaria());

        $tipo->setSlug('sesion-tipo-extraordinaria');
        $this->assertFalse($sesion->esOrdinaria());
    }
}
