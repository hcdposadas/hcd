<?php

namespace App\Repository;

use App\Entity\Comunicacion;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Comunicacion|null find($id, $lockMode = null, $lockVersion = null)
 * @method Comunicacion|null findOneBy(array $criteria, array $orderBy = null)
 * @method Comunicacion[]    findAll()
 * @method Comunicacion[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ComunicacionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Comunicacion::class);
    }

    public function countComunicacionesByTipo($tipo , $areaOrigen){

        return $this->createQueryBuilder('c')
                    ->select('COUNT(c.id) as cantidad')
                    ->andWhere('c.tipo = :tipo')
                    ->andWhere('c.areaOrigen = :areaOrigen')
                    ->setParameter('tipo', $tipo)
                    ->setParameter('areaOrigen', $areaOrigen)
                    ->getQuery()->getSingleScalarResult();
    }

    public function getQbAll() {
		$qb = $this->createQueryBuilder( 'e' );

		$qb->orderBy( 'e.id', 'DESC' );


//		TODO sacar si no encuentran expedientes

		return $qb;
	}

    public function getQbBuscar( $destino, $origen, $fecha , $texto ) {
        $qb = $this->getQbAll();
        $qb->join('e.areaDestino', 'd');
        if ( isset( $origen ) ) {
            $qb->andWhere( 'e.areaOrigen = :origen' )
               ->setParameter( 'origen', $origen );
        }
        if ( isset( $destino[0] ) ) {
            
               
            $qb->andWhere('d.id = :destino')
               ->setParameter( 'destino', $destino );
        }
        if ( isset( $fecha ) ) {
            $inicioDia = (clone $fecha)->setTime(0, 0, 0);
            $finDia = (clone $fecha)->setTime(23, 59, 59);
        
            $qb->andWhere('e.fecha BETWEEN :inicio AND :fin');
            $qb->setParameter('inicio', $inicioDia);
            $qb->setParameter('fin', $finDia);
        }
        if ( isset( $texto ) ) {
            $qb->andWhere( 'UPPER(e.estado) like :estado' )
               ->setParameter( 'estado', '%'.strtoupper($texto).'%' );
        }

return $qb;
}

    // /**
    //  * @return Comunicacion[] Returns an array of Comunicacion objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Comunicacion
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
