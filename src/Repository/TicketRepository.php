<?php

namespace App\Repository;

use App\Entity\Ticket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Ticket|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ticket|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ticket[]    findAll()
 * @method Ticket[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TicketRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ticket::class);
    }

    // /**
    //  * @return Ticket[] Returns an array of Ticket objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Ticket
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
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

    public function getQbBuscar( $destino, $origen=null, $fecha , $texto ) {
                $qb = $this->getQbAll();

                if ( isset( $origen ) ) {
                    $qb->andWhere( 'e.areaOrigen = :origen' )
                       ->setParameter( 'origen', $origen );
                }
                if ( isset( $destino ) ) {
                    $qb->andWhere( 'e.areaDestino = :destino' )
                       ->setParameter( 'destino', $destino );
                }
                if ( isset( $fecha ) ) {
                    $inicioDia = (clone $data['fechaPresentacion'])->setTime(0, 0, 0);
                    $finDia = (clone $data['fechaPresentacion'])->setTime(23, 59, 59);
                
                    $qb->andWhere('e.fechaPresentacion BETWEEN :inicio AND :fin');
                    $qb->setParameter('inicio', $inicioDia);
                    $qb->setParameter('fin', $finDia);
                }
                if ( isset( $texto ) ) {
                    $qb->andWhere( 'UPPER(e.texto) like :texto' )
                       ->setParameter( 'texto', '%'.strtoupper($texto).'%' );
                }

        return $qb;
    }

}
