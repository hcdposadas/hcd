<?php

namespace AppBundle\Controller;

use AppBundle\Entity\ProyectoBAE;
use MesaEntradaBundle\Entity\Expediente;
use MesaEntradaBundle\Entity\Giro;
use MesaEntradaBundle\Entity\GiroAdministrativo;
use MesaEntradaBundle\Entity\IniciadorExpediente;
use MesaEntradaBundle\Form\Filter\ApiExpedienteFilterType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ApiController extends Controller
{
    public function getExpedientesAction(Request $request)
    {
        $page = max(1, intval($request->get('pagina', 1)));
        $rpp = min(100, intval($request->get('rpp', 20)));

        $em = $this->getDoctrine()->getManager();

        $tipoExpediente = $em->getRepository('MesaEntradaBundle:TipoExpediente')->findOneBy([
            'slug' => 'externo'
        ]);

        $filterType = $this->createForm( ApiExpedienteFilterType::class, null, ['method' => 'GET']);
        $filterType->handleRequest( $request );

        $count = count($em->getRepository('MesaEntradaBundle:Expediente')
            ->getQbBuscar($filterType->getData(), $tipoExpediente)
            ->getQuery()
            ->getArrayResult());

        $expedientes = $em->getRepository('MesaEntradaBundle:Expediente')
            ->getQbBuscar($filterType->getData(), $tipoExpediente)
            ->getQuery()
            ->setFirstResult(($page - 1) * $rpp)
            ->setMaxResults($rpp)
            ->getResult();

        $expedientes = array_map(function (Expediente $expediente) use ($em) {
            if ($expediente->getIniciadores()->count()) {
                $iniciadores = $expediente->getIniciadores()->map(
                    function (IniciadorExpediente $ie) {
                        return $ie->getIniciador()
                            ? $ie->getIniciador()->getCargoPersona()->__toString()
                            : null;
                    }
                )->filter(function ($i) {
                    return $i != null;
                });
            } elseif ($expediente->getIniciadorParticular()) {
                $iniciadores = [
                    $expediente->getIniciadorParticular()->getNombreCompleto(),
                ];
            } else {
                $iniciadores = [
                    $expediente->getDependencia()->__toString(),
                ];
            }

            $numero = $expediente->getExpediente() . '-' . $expediente->getLetra(). '-' . ($expediente->getPeriodoLegislativo() ? $expediente->getPeriodoLegislativo()->__toString() : $expediente->getAnio());

            /** @var Giro[] $giros */
            $giros = $expediente->getGirosOrdenados();
            $ultimoGiro = null;
            if (count($giros)) {
                $ultimoGiro = [
                    'comision' => $giros[count($giros) - 1]->getComisionDestino()->getNombre(),
                    'fecha' => $giros[count($giros) - 1]->getFechaGiro()->format('Y-m-d H:i:s'),
                ];
            }

            /** @var GiroAdministrativo[] $giros */
            $giros = $expediente->getGiroAdministrativos();
            $ultimoGiroAdministrativo = null;
            if (count($giros)) {
                $ultimoGiroAdministrativo = [
                    'area' => $giros[count($giros) - 1]->getAreaDestino()->getNombre(),
                    'fecha' => $giros[count($giros) - 1]->getFechaGiro()->format('Y-m-d H:i:s'),
                ];
            }


            $ultimoGiroBae = null;
            $girosBae = $em->getRepository( ProyectoBAE::class )->findByExpediente( $expediente );
            if (count($girosBae)) {
                $giroBae = end($girosBae);
                $girosBae = $giroBae->getGiros();
                if (count($girosBae)) {
                    $ultimoGiroBae = [
                        'comision' => $girosBae[count($girosBae) - 1]->getComisionDestino()->getNombre(),
                        'fecha' => $girosBae[count($girosBae) - 1]->getFechaGiro()->format('Y-m-d H:i:s'),
                    ];
                }
            }

            return [
                'id' => $expediente->getId(),
                'numero' => $numero,
                'extracto' => $expediente->getExtracto(),
                'textoDefinitivo' => $expediente->getTextoDefinitivo(),
                'fecha' => $expediente->getFecha() ? $expediente->getFecha()->format('Y-m-d') : null,
                'registroMunicipal' => $expediente->getRegistroMunicipal(),
                'activo' => $expediente->getActivo(),
                'fechaCreacion' => $expediente->getFechaCreacion() ? $expediente->getFechaCreacion()->format('Y-m-d H:i:s') : null,
                'fechaActualizacion' => $expediente->getFechaActualizacion() ? $expediente->getFechaActualizacion()->format('Y-m-d H:i:s') : null,
                'iniciadores' => $iniciadores,
                'creadoPor' => $expediente->getCreadoPor() ? $expediente->getCreadoPor()->__toString() : null,
                'asignadoPor' => $expediente->getAsignadoPor() ? $expediente->getAsignadoPor()->__toString() : null,
                'ultimoGiro' => $ultimoGiro,
                'ultimoGiroAdministrativo' => $ultimoGiroAdministrativo,
                'ultimoGiroBae' => $ultimoGiroBae,
            ];
        }, $expedientes);

        return JsonResponse::create($expedientes, 200, ['x-pagination-count' => $count]);
    }
}
