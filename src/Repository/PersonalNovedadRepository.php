<?php

namespace App\Repository;

use App\Entity\PersonalNovedad;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method PersonalNovedad|null find($id, $lockMode = null, $lockVersion = null)
 * @method PersonalNovedad|null findOneBy(array $criteria, array $orderBy = null)
 * @method PersonalNovedad[]    findAll()
 * @method PersonalNovedad[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PersonalNovedadRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, PersonalNovedad::class);
    }

    // /**
    //  * @return PersonalNovedad[] Returns an array of PersonalNovedad objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('p.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?PersonalNovedad
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */

	public function getQbAll( $filter = null ) {
		$qb = $this->createQueryBuilder( 'pn' );

		if ( $filter['legajo'] ) {
			$qb->where( 'pn.legajo = :legajoId' )
			   ->setParameter( 'legajoId', $filter['legajo'] );
		}

		return $qb;
	}
}
