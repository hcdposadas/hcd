<?php

namespace VotacionBundle\Repository;

/**
 * MocionRepository
 */
class MocionRepository extends \Doctrine\ORM\EntityRepository
{
    public function findAll()
    {
        return $this->findBy(
            array(),
            array(
                'fecha' => 'DESC',
            )
        );
    }
}
