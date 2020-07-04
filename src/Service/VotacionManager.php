<?php

namespace App\Service;

use App\Entity\Mocion;
use App\Entity\Parametro;
use App\Entity\Votacion;
use App\Entity\Voto;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use App\Entity\Usuario;
use App\Service\NotificationsManager;

class VotacionManager
{
    /**
     * @var EntityManager
     */
    protected $entityManager;

    /**
     * @var NotificationsManager
     */
    protected $notificationsManager;

    protected $router;

    public function __construct(
        EntityManagerInterface $entityManager,
        NotificationsManager $notificationsManager,
        UrlGeneratorInterface $router,
        TipoMayoriaManager $tipoMayoriaManager
    )
    {
        $this->entityManager = $entityManager;
        $this->notificationsManager = $notificationsManager;
        $this->router = $router;
        $this->tipoMayoriaManager = $tipoMayoriaManager;
    }

    /**
     * @param Mocion $mocion
     *
     * @return Mocion
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function crear(Mocion $mocion)
    {
        $mocion->setNumero($this->getSiguienteNumero());
        $mocion->setEstado($this->getEstado(Mocion::ESTADO_PARA_VOTAR));
        $this->entityManager->persist($mocion);
        $this->entityManager->flush();

        return $mocion;
    }

    /**
     * @param Mocion $mocion
     *
     * @return Votacion|null
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function lanzar(Mocion $mocion)
    {
        if (!$mocion->puedeVotarse()) {
            if ($mocion->enVotacion()) {
                throw new \RuntimeException('No se puede lanzar la votación porque la moción ya se encuentra en votación');
            } elseif ($mocion->finalizada()) {
                throw new \RuntimeException('No se puede lanzar la votación porque la moción ya fue finalizada');
            } else {
                throw new \RuntimeException('No se puede lanzar votación');
            }
        }

        $enVotacion = $this->entityManager->getRepository(Mocion::class)->getEnVotacion();
        if ($enVotacion) {
            $urlToShow = $this->router->generate('mocion_show', ['id' => $enVotacion->getId()]);
            $mensaje = '<a href="' . $urlToShow . '">Nº ' . $enVotacion->getNumero() . '</a>';
            throw new \RuntimeException('No se puede lanzar la votación porque la Moción ' . $mensaje . ' se encuentra en votación.');
        }

        $duracion = $this->getTiempoMocion();

        $mocion->setEstado($this->getEstado(Mocion::ESTADO_EN_VOTACION));
        $votacion = new Votacion();
        $votacion->setMocion($mocion);
        $votacion->setDuracion($duracion);

        $this->entityManager->persist($votacion);
        $this->entityManager->persist($mocion);
        $this->entityManager->flush();

        $this->notificar($mocion, $duracion);

        return $votacion;
    }

    /**
     * @param Mocion $mocion
     *
     * @return Votacion|null
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function extender(Mocion $mocion)
    {
        if (!$mocion->enVotacion()) {
            throw new \RuntimeException('No se puede extender votación de la moción');
        }

        $duracion = $this->getTiempoMocionExtension();

        // Borra las abstenciones para permitir los posibles votos
        $mocion->getVotos()->filter(function (Voto $voto) {
            return $voto->esAbstencion();
        })->map(function (Voto $abstencion) {
            $this->entityManager->remove($abstencion);
        });

        $votacion = new Votacion();
        $votacion->setMocion($mocion);
        $votacion->setDuracion($duracion);

        $this->entityManager->persist($votacion);
        $this->entityManager->flush();

        $this->notificar($mocion, $duracion);

        return $votacion;
    }

    /**
     * @param Mocion $mocion
     *
     * @return Mocion|null
     * @throws \Doctrine\ORM\ORMException
     * @throws \Doctrine\ORM\OptimisticLockException
     * @throws \Doctrine\ORM\TransactionRequiredException
     */
    public function calcularResultados(Mocion $mocion)
    {
        if (!$mocion->enVotacion()) {
            throw new \RuntimeException('No se pueden calcular los resultados de esta moción');
        }

        $usuarioRepository = $this->entityManager->getRepository(Usuario::class);

        $presentes = array_map( function ( $id ) use ( $usuarioRepository ) {
            return $usuarioRepository->find( $id );
        },
            array_keys( $this->notificationsManager->hgetall( 'presentes' ) ) );

        $dedup = array();
        foreach ($presentes as $presente) {
            $dedup[$presente->getId()] = $presente;
        }
        $presentes = $dedup;
        unset($dedup);

        $votos = $mocion->getVotos();

        $noVotaron = array_filter($presentes,
            function ($presente) use ($votos) {
                foreach ($votos as $voto) {
                    if ($voto->getConcejal()->getId() == $presente->getId()) {
                        return false;
                    }
                }

                return true;
            });

        foreach ($noVotaron as $nv) {
            // La abstención se agrega a la votación y se persiste,
            // pero si la mocion se extiende, se elimina la abstención (en extender)
            // para permitir emitir los votos
            $voto = new Voto();
            $voto->setConcejal($nv);
            $voto->setValor(Voto::VOTO_ABSTENCION);
            $mocion->getVotos()->add($voto);
            $voto->setMocion($mocion);
            $voto->setVotacion($mocion->getVotaciones()->last());
            $this->entityManager->persist($voto);
        }
        $this->entityManager->flush();
        $mocion = $this->entityManager->find(Mocion::class, $mocion->getId());

        $total = 0;
        $afirmativos = 0;
        $negativos = 0;
        $abstenciones = 0;

        $votos = $mocion->getVotos();
        $votaronPositivo = [];
        $votaronNegativo = [];
        $aNoVotaron = [];

        foreach ($votos as $voto) {
            switch ($voto->getValor()) {
                case Voto::VOTO_AFIRMATIVO:
                    $total++;
                    $afirmativos++;
                    $votaronPositivo[] = strtoupper($voto->getConcejal()->getPersona()->getApellido());
                    break;
                case Voto::VOTO_NEGATIVO:
                    $total++;
                    $negativos++;
                    $votaronNegativo[] = strtoupper($voto->getConcejal()->getPersona()->getApellido());
                    break;
                case Voto::VOTO_ABSTENCION:
                    $total++;
                    $abstenciones++;
                    $aNoVotaron[] = strtoupper($voto->getConcejal()->getPersona()->getApellido());
                    break;
            }
        }

        $mocion->setCuentaAfirmativos($afirmativos);
        $mocion->setCuentaNegativos($negativos);
        $mocion->setCuentaAbstenciones($abstenciones);
        $mocion->setCuentaTotal($total);

        $mocion->setAprobado($this->tipoMayoriaManager->seAprueba($mocion));

        $this->entityManager->persist($mocion);
        $this->entityManager->flush();

        $textoMocion = '';
        if ($expediente = $mocion->getExpediente()) {
            $textoMocion = 'Expediente ' . $expediente . ': ' . $expediente->getExtracto();
        }

        $tipoMayoria = $mocion->getTipoMayoria();
        if ($tipoMayoria) {
            $tipoMayoria = 'Se aprueba con ' . $tipoMayoria;
        }

        $this->notificationsManager->notify('votacion.resultados',
            array(
                'mocion' => 'Moción Nº' . $mocion->__toString(),
                'textoMocion' => $textoMocion,
                'tipoMayoria' => $tipoMayoria,
                'sesion' => $mocion->getSesion()->__toString(),
                'afirmativos' => $mocion->getCuentaAfirmativos(),
                'negativos' => $mocion->getCuentaNegativos(),
                'abstenciones' => $mocion->getCuentaAbstenciones(),
                'total' => $mocion->getCuentaTotal(),
                'aprobado' => $mocion->isAprobado(),
                'votaronNegativo' => $votaronNegativo,
                'votaronPositivo' => $votaronPositivo,
                'seAbstuvieron' => $aNoVotaron,
            ));

        return $mocion;
    }

    /**
     * @param Mocion $mocion
     *
     * @return Mocion|null
     * @throws \Doctrine\ORM\OptimisticLockException
     */
    public function finalizar(Mocion $mocion)
    {
        if (!$mocion->enVotacion()) {
            throw new \RuntimeException('No se puede finalizar la votación de la moción');
        }

        $mocion->setEstado($this->getEstado(Mocion::ESTADO_FINALIZADO));

        $this->entityManager->persist($mocion);
        $this->entityManager->flush();

        $this->notificationsManager->notify('votacion.finalizada', array());

        return $mocion;
    }

    /**
     * @param Mocion $mocion
     * @param Usuario $usuario
     * @param $valorVoto
     *
     * @return Voto
     * @throws Exception
     */
    public function votar(Mocion $mocion, Usuario $usuario, $valorVoto)
    {
        // TODO verificar que el usuario sea concejal

        if (!$mocion->enVotacion()) {
            throw new Exception('La moción no se encuentra en votación');
        }

        $votacion = null;
        foreach ($mocion->getVotaciones() as $votacion) {
            if (!$votacion->finalizada()) {
                break;
            }
        }

        if (!$votacion || $votacion->finalizada()) {
            throw new Exception('La moción no se encuentra en votación en este momento');
        }

        if (!in_array($valorVoto, array(Voto::VOTO_AFIRMATIVO, Voto::VOTO_NEGATIVO))) {
            throw new Exception('El valor del voto no es válido');
        }

        foreach ($mocion->getVotos() as $voto) {
            if ($voto->getCreadoPor()->getId() == $usuario->getId()) {
                throw new Exception('No se puede votar dos veces la misma moción');
            }
        }

        $voto = new Voto();
        $voto->setValor($valorVoto);
        $voto->setMocion($mocion);
        $voto->setVotacion($votacion);
        $voto->setConcejal($usuario);

        $this->entityManager->persist($voto);
        $this->entityManager->flush();

        return $voto;
    }

    /**
     * @param $estado
     *
     * @return Parametro|null
     */
    protected function getEstado($estado)
    {
        return $this->entityManager->getRepository(Parametro::class)->getBySlug($estado);
    }

    /**
     * @return integer
     */
    protected function getTiempoMocion()
    {
        /** @var Parametro $tiempo */
        $tiempo = $this->entityManager->getRepository(Parametro::class)->getBySlug('tiempo-mocion');
        return $tiempo ? $tiempo->getValor() : 10;
    }

    /**
     * @return integer
     */
    protected function getTiempoMocionExtension()
    {
        /** @var Parametro $tiempo */
        $tiempo = $this->entityManager->getRepository(Parametro::class)->getBySlug('tiempo-mocion-extension');
        return $tiempo ? $tiempo->getValor() : 5;
    }

    /**
     * @return int
     */
    protected function getSiguienteNumero()
    {
        return $this->entityManager->getRepository(Mocion::class)->siguienteNumero();
    }

    /**
     * @param Mocion $mocion
     * @param $duracion
     */
    protected function notificar(Mocion $mocion, $duracion)
    {
        $textoMocion = '';
        if ($expediente = $mocion->getExpediente()) {
            $textoMocion = 'Expediente ' . $expediente . ': ' . $expediente->getExtracto();
        }

        $tipoMayoria = $mocion->getTipoMayoria();
        if ($tipoMayoria) {
            $tipoMayoria = 'Se aprueba con ' . $tipoMayoria;
        }

        $this->notificationsManager->notify('votacion.abierta',
            array(
                'mocion' => 'Moción Nº' . $mocion->__toString(),
                'textoMocion' => $textoMocion,
                'tipoMayoria' => $tipoMayoria,
                'sesion' => $mocion->getSesion()->__toString(),
                'duracion' => $duracion
            ));

        $this->notificationsManager->notify('votacion.cerrada', null, $duracion);
    }
}
