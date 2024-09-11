<?php

namespace App\Repository;

use App\Entity\RecibidoComunicado;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method RecibidoComunicado|null find($id, $lockMode = null, $lockVersion = null)
 * @method RecibidoComunicado|null findOneBy(array $criteria, array $orderBy = null)
 * @method RecibidoComunicado[]    findAll()
 * @method RecibidoComunicado[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class RecibidoComunicadoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, RecibidoComunicado::class);
    }

    // /**
    //  * @return RecibidoComunicado[] Returns an array of RecibidoComunicado objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('r.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?RecibidoComunicado
    {
        return $this->createQueryBuilder('r')
            ->andWhere('r.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

    public function getQbAll() {
		$qb = $this->createQueryBuilder( 'e' );

		$qb->orderBy( 'e.id', 'DESC' );


//		TODO sacar si no encuentran expedientes

		return $qb;
	}

    public function getQbBuscar( $destino, $origen, $fecha , $texto ) {
        $qb = $this->getQbAll();
        $qb->join('e.comunicacion', 'c');
        if ( isset( $origen ) ) {
            $qb->andWhere( 'c.areaOrigen = :origen' )
               ->setParameter( 'origen', $origen );
        }
        if ( isset( $destino ) ) {
            
               
            $qb->andWhere('e.area = :destino')
               ->setParameter( 'destino', $destino );
        }
        if ( isset( $fecha ) ) {
            $inicioDia = (clone $fecha)->setTime(0, 0, 0);
            $finDia = (clone $fecha)->setTime(23, 59, 59);
        
            $qb->andWhere('c.fecha BETWEEN :inicio AND :fin');
            $qb->setParameter('inicio', $inicioDia);
            $qb->setParameter('fin', $finDia);
        }
        if ( isset( $texto ) ) {
            $qb->andWhere( 'UPPER(c.estado) like :estado' )
               ->setParameter( 'estado', '%'.strtoupper($texto).'%' );
        }

return $qb;
}
}
