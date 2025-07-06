<?php

namespace App\Repository;

use App\Entity\NoConformidad;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method NoConformidad|null find($id, $lockMode = null, $lockVersion = null)
 * @method NoConformidad|null findOneBy(array $criteria, array $orderBy = null)
 * @method NoConformidad[]    findAll()
 * @method NoConformidad[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class NoConformidadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, NoConformidad::class);
    }

    public function getQbAll() {
        $qb = $this->createQueryBuilder('e');
        $qb->orderBy('e.id', 'DESC');
        return $qb;
    }

    public function getQbBuscar($area = null, $empleado = null, $origen = null, $categoria = null, $fecha = null) {
        $qb = $this->getQbAll();

        if ($area) {
            $qb->andWhere('e.area = :area')
               ->setParameter('area', $area);
        }

        if ($empleado) {
            $qb->andWhere('e.empleado = :empleado')
               ->setParameter('empleado', $empleado);
        }

        if ($origen) {
            $qb->andWhere('e.origen = :origen')
               ->setParameter('origen', $origen);
        }

        if ($categoria) {
            $qb->andWhere('e.categoria = :categoria')
               ->setParameter('categoria', $categoria);
        }

        if ($fecha) {
            $inicioDia = (clone $fecha)->setTime(0, 0, 0);
            $finDia = (clone $fecha)->setTime(23, 59, 59);
        
            $qb->andWhere('e.fecha BETWEEN :inicio AND :fin');
            $qb->setParameter('inicio', $inicioDia);
            $qb->setParameter('fin', $finDia);
        }

        return $qb;
    }
} 