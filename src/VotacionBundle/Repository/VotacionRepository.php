<?php

namespace VotacionBundle\Repository;

/**
 * VotacionRepository
 */
class VotacionRepository extends \Doctrine\ORM\EntityRepository
{
    public function findAll()
    {
        return $this->findBy(
            array(),
            array(
                'desde' => 'DESC',
            )
        );
    }
}
