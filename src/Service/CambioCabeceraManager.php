<?php

namespace App\Service;

use App\Entity\Comision;
use App\Entity\Giro;
use App\Entity\ProyectoBAE;
use App\Entity\Sesion;
use Doctrine\ORM\EntityManagerInterface;

/**
 * Cambio de la comisión de cabecera de un expediente, registrado sobre su
 * ProyectoBAE en la sesión ordinaria donde se trató.
 *
 * Diseño: docs/superpowers/specs/2026-09-22-cambio-de-cabecera-design.md
 */
class CambioCabeceraManager
{
    /** @var EntityManagerInterface */
    private $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    /**
     * Comisión que era cabecera antes de la sesión del ProyectoBAE.
     * Si el cambio ya está registrado devuelve la guardada; si no, la calcula.
     */
    public function cabeceraAnterior(ProyectoBAE $proyectoBae): ?Comision
    {
        if ($proyectoBae->getEsCambioCabecera()) {
            return $proyectoBae->getComisionCabeceraAnterior();
        }

        $cabecera = null;
        foreach ($this->girosDeSesionesAnteriores($proyectoBae) as $giro) {
            if ($giro->getCabecera()) {
                $cabecera = $giro->getComisionDestino();
            }
        }
        if ($cabecera !== null) {
            return $cabecera;
        }

        foreach ($proyectoBae->getExpediente()->getGiros() as $giro) {
            if ($giro->getCabecera()) {
                return $giro->getComisionDestino();
            }
        }

        return null;
    }

    /**
     * Registra el cambio (alta) o corrige la comisión (edición). No hace flush.
     *
     * @throws \DomainException con un mensaje para mostrar al usuario
     */
    public function aplicar(ProyectoBAE $proyectoBae, Comision $nueva): void
    {
        $sesion = $this->sesionDe($proyectoBae);
        if ($sesion === null || !$sesion->esOrdinaria()) {
            throw new \DomainException('El cambio de cabecera solo se registra en sesiones ordinarias.');
        }
        if (!$nueva->getActivo()) {
            throw new \DomainException('La comisión elegida no está activa.');
        }
        $anterior = $this->cabeceraAnterior($proyectoBae);
        if ($anterior === $nueva) {
            throw new \DomainException('La comisión elegida ya es la cabecera.');
        }

        if ($proyectoBae->getEsCambioCabecera()) {
            $this->quitarGiroAgregado($proyectoBae, $nueva);
        } else {
            $this->completarComisionesGiradas($proyectoBae);
            $proyectoBae->setEsCambioCabecera(true);
            $proyectoBae->setComisionCabeceraAnterior($anterior);
        }

        $this->marcarCabecera($proyectoBae, $nueva);
    }

    /**
     * Agrega, sin cabecera, las comisiones a las que el expediente ya estaba
     * girado y que faltan en este ProyectoBAE.
     */
    private function completarComisionesGiradas(ProyectoBAE $proyectoBae): void
    {
        $girosPrevios = array_merge(
            $this->girosDeSesionesAnteriores($proyectoBae),
            $proyectoBae->getExpediente()->getGiros()->toArray()
        );

        foreach ($girosPrevios as $giro) {
            $comision = $giro->getComisionDestino();
            if ($comision !== null && $this->giroDe($proyectoBae, $comision) === null) {
                $this->agregarGiro($proyectoBae, $comision);
            }
        }
    }

    /**
     * Edición: si el cambio había agregado un giro para otra comisión, lo saca.
     */
    private function quitarGiroAgregado(ProyectoBAE $proyectoBae, Comision $nueva): void
    {
        $agregado = $proyectoBae->getGiroCabeceraAgregado();
        if ($agregado === null || $agregado->getComisionDestino() === $nueva) {
            return;
        }

        $proyectoBae->removeGiro($agregado);
        $agregado->setProyectoBae(null);
        $proyectoBae->setGiroCabeceraAgregado(null);
        $this->em->remove($agregado);
    }

    /**
     * Deja a la comisión nueva como única cabecera del ProyectoBAE.
     */
    private function marcarCabecera(ProyectoBAE $proyectoBae, Comision $nueva): void
    {
        $giroNueva = $this->giroDe($proyectoBae, $nueva);
        if ($giroNueva === null) {
            $giroNueva = $this->agregarGiro($proyectoBae, $nueva);
            $proyectoBae->setGiroCabeceraAgregado($giroNueva);
        }

        foreach ($proyectoBae->getGiros() as $giro) {
            $giro->setCabecera($giro === $giroNueva);
        }
    }

    private function agregarGiro(ProyectoBAE $proyectoBae, Comision $comision): Giro
    {
        $giro = new Giro();
        $giro->setComisionDestino($comision);
        $giro->setCabecera(false);
        $giro->setFechaGiro(clone $this->sesionDe($proyectoBae)->getFecha());
        $giro->setOrden($this->siguienteOrden($proyectoBae));

        $proyectoBae->addGiro($giro);
        $this->em->persist($giro);

        return $giro;
    }

    private function siguienteOrden(ProyectoBAE $proyectoBae): int
    {
        $maximo = 0;
        foreach ($proyectoBae->getGiros() as $giro) {
            $maximo = max($maximo, (int) $giro->getOrden());
        }

        return $maximo + 1;
    }

    private function giroDe(ProyectoBAE $proyectoBae, Comision $comision): ?Giro
    {
        foreach ($proyectoBae->getGiros() as $giro) {
            if ($giro->getComisionDestino() === $comision) {
                return $giro;
            }
        }

        return null;
    }

    private function sesionDe(ProyectoBAE $proyectoBae): ?Sesion
    {
        $bae = $proyectoBae->getBoletinAsuntoEntrado();

        return $bae !== null ? $bae->getSesion() : null;
    }

    /**
     * @return Giro[] de la sesión más vieja a la más nueva
     */
    private function girosDeSesionesAnteriores(ProyectoBAE $proyectoBae): array
    {
        $sesion = $this->sesionDe($proyectoBae);
        if ($sesion === null) {
            return [];
        }

        return $this->em->getRepository(Giro::class)
            ->findGirosDeSesionesAnteriores($proyectoBae->getExpediente(), $sesion);
    }
}
