<?php

namespace App\Tests\Service;

use App\Entity\BoletinAsuntoEntrado;
use App\Entity\Comision;
use App\Entity\Expediente;
use App\Entity\Giro;
use App\Entity\Parametro;
use App\Entity\ProyectoBAE;
use App\Entity\Sesion;
use App\Repository\GiroRepository;
use App\Service\CambioCabeceraManager;
use Doctrine\ORM\EntityManagerInterface;
use PHPUnit\Framework\MockObject\MockObject;
use PHPUnit\Framework\TestCase;

class CambioCabeceraManagerTest extends TestCase
{
    /** @var Giro[] lo que devuelve GiroRepository::findGirosDeSesionesAnteriores */
    private $girosAnteriores = [];

    /** @var Giro[] lo que devuelve GiroRepository::findGirosDeSesionesAnterioresDeExpedientes */
    private $girosAnterioresDeExpedientes = [];

    /** @var Giro[] lo que devuelve GiroRepository::findGirosDirectosDeExpedientes */
    private $girosDirectosDeExpedientes = [];

    /** @var EntityManagerInterface&MockObject */
    private $em;

    /** @var CambioCabeceraManager */
    private $manager;

    protected function setUp(): void
    {
        $this->girosAnteriores = [];
        $this->girosAnterioresDeExpedientes = [];
        $this->girosDirectosDeExpedientes = [];

        $repositorio = $this->createMock(GiroRepository::class);
        $repositorio->method('findGirosDeSesionesAnteriores')
            ->willReturnCallback(function () {
                return $this->girosAnteriores;
            });
        $repositorio->method('findGirosDeSesionesAnterioresDeExpedientes')
            ->willReturnCallback(function () {
                return $this->girosAnterioresDeExpedientes;
            });
        $repositorio->method('findGirosDirectosDeExpedientes')
            ->willReturnCallback(function () {
                return $this->girosDirectosDeExpedientes;
            });

        $this->em = $this->createMock(EntityManagerInterface::class);
        $this->em->method('getRepository')->with(Giro::class)->willReturn($repositorio);

        $this->manager = new CambioCabeceraManager($this->em);
    }

    public function testCabeceraAnteriorEsLaUltimaCabeceraDeSesionesAnteriores(): void
    {
        $a = $this->comision('Obras');
        $b = $this->comision('Hacienda');
        $this->girosAnteriores = [$this->giro($a, true), $this->giro($this->comision('Salud')), $this->giro($b, true)];

        $this->assertSame($b, $this->manager->cabeceraAnterior($this->proyectoBae()));
    }

    public function testCabeceraAnteriorUsaLosGirosDelExpedienteSiNoHayEnSesiones(): void
    {
        $d = $this->comision('Legislación');
        $expediente = new Expediente();
        $expediente->addGiro($this->giro($d, true));
        $this->girosAnteriores = [$this->giro($this->comision('Salud'))];

        $this->assertSame($d, $this->manager->cabeceraAnterior($this->proyectoBae(null, $expediente)));
    }

    public function testCabeceraAnteriorEsNullSiNuncaTuvoCabecera(): void
    {
        $this->assertNull($this->manager->cabeceraAnterior($this->proyectoBae()));
    }

    public function testCabeceraAnteriorDevuelveLaGuardadaSiElCambioYaExiste(): void
    {
        $a = $this->comision('Obras');
        $this->girosAnteriores = [$this->giro($this->comision('Hacienda'), true)];
        $proyectoBae = $this->proyectoBae();
        $proyectoBae->setEsCambioCabecera(true);
        $proyectoBae->setComisionCabeceraAnterior($a);

        $this->assertSame($a, $this->manager->cabeceraAnterior($proyectoBae));
    }

    public function testCabeceraAnteriorPrefiereElGiroPropioDelProyectoBaeALosDeSesionesAnteriores(): void
    {
        $x = $this->comision('Obras');
        $y = $this->comision('Hacienda');
        $this->girosAnteriores = [$this->giro($y, true)];
        $proyectoBae = $this->proyectoBae();
        $proyectoBae->addGiro($this->giro($x, true));

        $this->assertSame($x, $this->manager->cabeceraAnterior($proyectoBae));
    }

    public function testAplicarConLaComisionDelGiroPropioRechazaSinTocarNada(): void
    {
        $x = $this->comision('Obras');
        $y = $this->comision('Hacienda');
        $this->girosAnteriores = [$this->giro($y, true)];
        $proyectoBae = $this->proyectoBae();
        $proyectoBae->addGiro($this->giro($x, true));

        try {
            $this->manager->aplicar($proyectoBae, $x);
            $this->fail('Se esperaba \DomainException');
        } catch (\DomainException $e) {
            $this->assertSame('La comisión elegida ya es la cabecera.', $e->getMessage());
        }

        $this->assertSame([$x], $this->comisionesDe($proyectoBae));
        $this->assertNull($proyectoBae->getEsCambioCabecera());
    }

    public function testAplicarConOtraComisionGuardaElGiroPropioComoCabeceraAnterior(): void
    {
        $x = $this->comision('Obras');
        $y = $this->comision('Hacienda');
        $z = $this->comision('Salud');
        $this->girosAnteriores = [$this->giro($y, true)];
        $proyectoBae = $this->proyectoBae();
        $proyectoBae->addGiro($this->giro($x, true));

        $this->manager->aplicar($proyectoBae, $z);

        $this->assertSame($x, $proyectoBae->getComisionCabeceraAnterior());
    }

    public function testRechazaSesionQueNoEsOrdinaria(): void
    {
        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('El cambio de cabecera solo se registra en sesiones ordinarias.');

        $this->manager->aplicar($this->proyectoBae($this->sesion('sesion-tipo-extraordinaria')), $this->comision('Obras'));
    }

    public function testRechazaComisionInactiva(): void
    {
        $inactiva = $this->comision('Obras');
        $inactiva->setActivo(false);

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('La comisión elegida no está activa.');

        $this->manager->aplicar($this->proyectoBae(), $inactiva);
    }

    public function testAplicarRechazaProyectoSinExpedienteSinTocarNada(): void
    {
        $proyectoBae = $this->proyectoBae();
        $proyectoBae->setExpediente(null);

        try {
            $this->manager->aplicar($proyectoBae, $this->comision('Obras'));
            $this->fail('Se esperaba \DomainException');
        } catch (\DomainException $e) {
            $this->assertSame('El proyecto no tiene expediente asociado.', $e->getMessage());
        }

        $this->assertCount(0, $proyectoBae->getGiros());
        $this->assertNull($proyectoBae->getEsCambioCabecera());
    }

    public function testRechazaSiLaComisionElegidaYaEsLaCabeceraSinTocarNada(): void
    {
        $a = $this->comision('Obras');
        $this->girosAnteriores = [$this->giro($a, true)];
        $proyectoBae = $this->proyectoBae();

        try {
            $this->manager->aplicar($proyectoBae, $a);
            $this->fail('Se esperaba \DomainException');
        } catch (\DomainException $e) {
            $this->assertSame('La comisión elegida ya es la cabecera.', $e->getMessage());
        }

        $this->assertCount(0, $proyectoBae->getGiros());
        $this->assertNull($proyectoBae->getEsCambioCabecera());
    }

    public function testAltaConComisionNoGiradaLaAgregaComoUnicaCabecera(): void
    {
        $a = $this->comision('Obras');
        $c = $this->comision('Salud');
        $b = $this->comision('Hacienda');
        $this->girosAnteriores = [$this->giro($a, true), $this->giro($c)];
        $proyectoBae = $this->proyectoBae();

        $this->manager->aplicar($proyectoBae, $b);

        $this->assertSame([$a, $c, $b], $this->comisionesDe($proyectoBae));
        $this->assertSame([false, false, true], $this->marcasDe($proyectoBae));
        $this->assertTrue($proyectoBae->getEsCambioCabecera());
        $this->assertSame($a, $proyectoBae->getComisionCabeceraAnterior());

        $giroB = $proyectoBae->getGiroCabecera();
        $this->assertSame($b, $giroB->getComisionDestino());
        $this->assertSame($giroB, $proyectoBae->getGiroCabeceraAgregado());
        $this->assertSame($proyectoBae, $giroB->getProyectoBae());
        $this->assertEquals(new \DateTime('2026-09-15'), $giroB->getFechaGiro());
        $this->assertSame(3, $giroB->getOrden());
    }

    public function testAltaConComisionYaGiradaSoloMueveLaMarca(): void
    {
        $a = $this->comision('Obras');
        $b = $this->comision('Hacienda');
        $this->girosAnteriores = [$this->giro($a, true), $this->giro($b)];
        $proyectoBae = $this->proyectoBae();

        $this->manager->aplicar($proyectoBae, $b);

        $this->assertSame([$a, $b], $this->comisionesDe($proyectoBae));
        $this->assertSame([false, true], $this->marcasDe($proyectoBae));
        $this->assertNull($proyectoBae->getGiroCabeceraAgregado());
    }

    public function testAltaRespetaLosGirosYaCargadosYCompletaLosQueFaltan(): void
    {
        $a = $this->comision('Obras');
        $c = $this->comision('Salud');
        $b = $this->comision('Hacienda');
        $this->girosAnteriores = [$this->giro($a, true), $this->giro($c)];
        $proyectoBae = $this->proyectoBae();
        $giroCargado = $this->giro($a, true, 1);
        $proyectoBae->addGiro($giroCargado);

        $this->manager->aplicar($proyectoBae, $b);

        $this->assertSame([$a, $c, $b], $this->comisionesDe($proyectoBae));
        $this->assertSame($giroCargado, $proyectoBae->getGiros()->first());
        $this->assertFalse($giroCargado->getCabecera());
    }

    public function testAltaSinCabeceraAnteriorRegistraElCambio(): void
    {
        $b = $this->comision('Hacienda');
        $proyectoBae = $this->proyectoBae();

        $this->manager->aplicar($proyectoBae, $b);

        $this->assertTrue($proyectoBae->getEsCambioCabecera());
        $this->assertNull($proyectoBae->getComisionCabeceraAnterior());
        $this->assertSame([$b], $this->comisionesDe($proyectoBae));
        $this->assertSame($b, $proyectoBae->getGiroCabecera()->getComisionDestino());
    }

    public function testEdicionQuitaElGiroQueHabiaAgregadoElCambio(): void
    {
        $a = $this->comision('Obras');
        $b = $this->comision('Hacienda');
        $d = $this->comision('Legislación');
        $this->girosAnteriores = [$this->giro($a, true)];
        $proyectoBae = $this->proyectoBae();
        $proyectoBae->setFirmado('hoja-firmada.pdf');

        $this->manager->aplicar($proyectoBae, $b);
        $giroB = $proyectoBae->getGiroCabeceraAgregado();

        $this->em->expects($this->once())->method('remove')->with($this->identicalTo($giroB));

        $this->manager->aplicar($proyectoBae, $d);

        $this->assertSame([$a, $d], $this->comisionesDe($proyectoBae));
        $this->assertSame($d, $proyectoBae->getGiroCabecera()->getComisionDestino());
        $this->assertSame($d, $proyectoBae->getGiroCabeceraAgregado()->getComisionDestino());
        $this->assertNull($giroB->getProyectoBae());
        $this->assertSame($a, $proyectoBae->getComisionCabeceraAnterior());
        $this->assertSame('hoja-firmada.pdf', $proyectoBae->getFirmado());
    }

    public function testEdicionConservaLaComisionQueYaEstabaGirada(): void
    {
        $a = $this->comision('Obras');
        $b = $this->comision('Hacienda');
        $c = $this->comision('Salud');
        $this->girosAnteriores = [$this->giro($a, true), $this->giro($b)];
        $proyectoBae = $this->proyectoBae();

        $this->manager->aplicar($proyectoBae, $b);

        $this->em->expects($this->never())->method('remove');

        $this->manager->aplicar($proyectoBae, $c);

        $this->assertSame([$a, $b, $c], $this->comisionesDe($proyectoBae));
        $this->assertSame([false, false, true], $this->marcasDe($proyectoBae));
    }

    public function testEdicionRechazaVolverALaCabeceraAnterior(): void
    {
        $a = $this->comision('Obras');
        $this->girosAnteriores = [$this->giro($a, true)];
        $proyectoBae = $this->proyectoBae();
        $this->manager->aplicar($proyectoBae, $this->comision('Hacienda'));

        $this->expectException(\DomainException::class);
        $this->expectExceptionMessage('La comisión elegida ya es la cabecera.');

        $this->manager->aplicar($proyectoBae, $a);
    }

    public function testCabecerasAnterioresDevuelveElMapaCorrectoParaVariosProyectos(): void
    {
        $x = $this->comision('Obras');
        $y = $this->comision('Hacienda');

        $expedienteConCabeceraPropia = new Expediente();
        $proyectoBae1 = $this->conId($this->proyectoBae(null, $expedienteConCabeceraPropia), 10);
        $proyectoBae1->addGiro($this->giro($x, true));

        $expedienteQueHeredaDeSesionAnterior = new Expediente();
        $proyectoBae2 = $this->conId($this->proyectoBae(null, $expedienteQueHeredaDeSesionAnterior), 20);
        $proyectoBaeDeSesionAnterior = $this->proyectoBae(null, $expedienteQueHeredaDeSesionAnterior);
        $proyectoBaeDeSesionAnterior->addGiro($this->giro($y, true));
        $this->girosAnterioresDeExpedientes = $proyectoBaeDeSesionAnterior->getGiros()->toArray();

        $resultado = $this->manager->cabecerasAnteriores([$proyectoBae1, $proyectoBae2]);

        $this->assertSame([10 => $x, 20 => $y], $resultado);
    }

    public function testCabecerasAnterioresDejaEnNullElProyectoSinExpediente(): void
    {
        $proyectoBae = $this->conId($this->proyectoBae(), 30);
        $proyectoBae->setExpediente(null);

        $resultado = $this->manager->cabecerasAnteriores([$proyectoBae]);

        $this->assertArrayHasKey(30, $resultado);
        $this->assertNull($resultado[30]);
    }

    private function sesion(string $slugTipo = Sesion::SLUG_TIPO_ORDINARIA): Sesion
    {
        $tipo = new Parametro();
        $tipo->setSlug($slugTipo);

        $sesion = new Sesion();
        $sesion->setTitulo('Sesión Ordinaria');
        $sesion->setFecha(new \DateTime('2026-09-15'));
        $sesion->setTipoSesion($tipo);

        return $sesion;
    }

    private function proyectoBae(?Sesion $sesion = null, ?Expediente $expediente = null): ProyectoBAE
    {
        $bae = new BoletinAsuntoEntrado();
        $bae->setSesion($sesion ?? $this->sesion());

        $proyectoBae = new ProyectoBAE();
        $proyectoBae->setBoletinAsuntoEntrado($bae);
        $proyectoBae->setExpediente($expediente ?? new Expediente());

        return $proyectoBae;
    }

    /**
     * Fuerza el id de un ProyectoBAE creado en memoria (sin base de datos no
     * hay forma de que Doctrine lo asigne), para poder probar el mapa por id
     * que arma cabecerasAnteriores().
     */
    private function conId(ProyectoBAE $proyectoBae, int $id): ProyectoBAE
    {
        $propiedad = new \ReflectionProperty(ProyectoBAE::class, 'id');
        $propiedad->setAccessible(true);
        $propiedad->setValue($proyectoBae, $id);

        return $proyectoBae;
    }

    private function comision(string $nombre): Comision
    {
        $comision = new Comision();
        $comision->setNombre($nombre);

        return $comision;
    }

    private function giro(Comision $comision, bool $cabecera = false, ?int $orden = null): Giro
    {
        $giro = new Giro();
        $giro->setComisionDestino($comision);
        $giro->setCabecera($cabecera);
        $giro->setOrden($orden);

        return $giro;
    }

    /** @return Comision[] en el orden de la colección de giros */
    private function comisionesDe(ProyectoBAE $proyectoBae): array
    {
        return array_values(array_map(function (Giro $giro) {
            return $giro->getComisionDestino();
        }, $proyectoBae->getGiros()->toArray()));
    }

    /** @return bool[] la marca de cabecera de cada giro, en orden */
    private function marcasDe(ProyectoBAE $proyectoBae): array
    {
        return array_values(array_map(function (Giro $giro) {
            return (bool) $giro->getCabecera();
        }, $proyectoBae->getGiros()->toArray()));
    }
}
