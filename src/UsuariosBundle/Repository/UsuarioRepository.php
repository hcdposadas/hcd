<?php
/**
 * Created by PhpStorm.
 * User: matias
 * Date: 22/2/18
 * Time: 12:19
 */

namespace UsuariosBundle\Repository;


use Doctrine\ORM\EntityRepository;
use Symfony\Bridge\Doctrine\Security\User\UserLoaderInterface;

class UsuarioRepository extends EntityRepository implements UserLoaderInterface {
	public function loadUserByUsername( $password ) {
		return $this->createQueryBuilder( 'u' )
		            ->where( 'u.password = :password ' )
		            ->setParameter( 'password', $password )
		            ->getQuery()
		            ->getOneOrNullResult();
	}
}