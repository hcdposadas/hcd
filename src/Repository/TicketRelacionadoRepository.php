<?php

namespace App\Repository;

use App\Entity\Ticket;
use App\Entity\TicketRelacionado;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method TicketRelacionado|null find($id, $lockMode = null, $lockVersion = null)
 * @method TicketRelacionado|null findOneBy(array $criteria, array $orderBy = null)
 * @method TicketRelacionado[]    findAll()
 * @method TicketRelacionado[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TicketRelacionadoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TicketRelacionado::class);
    }

    /**
     * Busca todos los tickets relacionados a un ticket específico
     * (ya sea como origen o como destino)
     *
     * @param Ticket $ticket
     * @return TicketRelacionado[]
     */
    public function findByTicket(Ticket $ticket)
    {
        return $this->createQueryBuilder('tr')
            ->where('tr.ticketOrigen = :ticket OR tr.ticketDestino = :ticket')
            ->setParameter('ticket', $ticket)
            ->orderBy('tr.fechaCreacion', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Verifica si existe una relación entre dos tickets
     *
     * @param Ticket $ticket1
     * @param Ticket $ticket2
     * @return bool
     */
    public function existeRelacion(Ticket $ticket1, Ticket $ticket2): bool
    {
        $count = $this->createQueryBuilder('tr')
            ->select('COUNT(tr.id)')
            ->where('(tr.ticketOrigen = :ticket1 AND tr.ticketDestino = :ticket2) OR (tr.ticketOrigen = :ticket2 AND tr.ticketDestino = :ticket1)')
            ->setParameter('ticket1', $ticket1)
            ->setParameter('ticket2', $ticket2)
            ->getQuery()
            ->getSingleScalarResult();
        
        return $count > 0;
    }
} 