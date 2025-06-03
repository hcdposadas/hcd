<?php

namespace App\Repository;

use App\Entity\Ticket;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query\Expr\Join;

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

    /**
     * @return Ticket[] Returns an array of Ticket objects with related tickets loaded recursively
     */
    public function findByAreaOrigenWithRelated($areaOrigen, $orderBy = null)
    {
        $qb = $this->createQueryBuilder('t')
            ->andWhere('t.areaOrigen = :areaOrigen')
            ->setParameter('areaOrigen', $areaOrigen);
            
        // Aplicar ordenamiento si se proporciona
        if ($orderBy) {
            foreach ($orderBy as $field => $order) {
                $qb->orderBy('t.' . $field, $order);
            }
        }
        
        $tickets = $qb->getQuery()->getResult();
        
        // Cargar los tickets relacionados recursivamente
        foreach ($tickets as $ticket) {
            $this->loadRelatedTicketsRecursively($ticket);
        }
        
        return $tickets;
    }

    /**
     * @return Ticket[] Returns an array of Ticket objects with related tickets loaded recursively
     */
    public function findByAreaDestinoWithRelated($areaDestino, $orderBy = null)
    {
        $qb = $this->createQueryBuilder('t')
            ->andWhere('t.areaDestino = :areaDestino')
            ->setParameter('areaDestino', $areaDestino);
            
        // Aplicar ordenamiento si se proporciona
        if ($orderBy) {
            foreach ($orderBy as $field => $order) {
                $qb->orderBy('t.' . $field, $order);
            }
        }
        
        $tickets = $qb->getQuery()->getResult();
        
        // Cargar los tickets relacionados recursivamente
        foreach ($tickets as $ticket) {
            $this->loadRelatedTicketsRecursively($ticket);
        }
        
        return $tickets;
    }

    /**
     * Carga recursivamente todos los tickets relacionados (hijos, nietos, etc.)
     */
    private function loadRelatedTicketsRecursively($ticket, $depth = 0, $maxDepth = 5)
    {
        // Evitar bucles infinitos y limitar la profundidad
        if ($depth >= $maxDepth) {
            return;
        }
        
        // Cargar tickets relacionados (hijos)
        if (!$ticket->getTicketsRelacionados()->isEmpty()) {
            $related = $this->createQueryBuilder('t')
                ->andWhere('t.ticketPadre = :padre')
                ->setParameter('padre', $ticket)
                ->getQuery()
                ->getResult();
                
            foreach ($related as $child) {
                // Cargar recursivamente los hijos de este ticket
                $this->loadRelatedTicketsRecursively($child, $depth + 1, $maxDepth);
            }
        }
        
        // Cargar ticket padre si existe
        if ($ticket->getTicketPadre()) {
            $padre = $this->find($ticket->getTicketPadre()->getId());
            if ($padre) {
                // No cargamos recursivamente hacia arriba para evitar bucles
                // Solo queremos asegurarnos de que el padre esté cargado
            }
        }
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
                    $inicioDia = (clone $fecha)->setTime(0, 0, 0);
                    $finDia = (clone $fecha)->setTime(23, 59, 59);
                
                    $qb->andWhere('e.fecha BETWEEN :inicio AND :fin');
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
