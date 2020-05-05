<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Mocion;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;

class AdminController extends BaseAdminController {
	public function createNewUsuarioEntity() {
		return $this->get( 'fos_user.user_manager' )->createUser();
	}

	public function updateUsuarioEntity( $user ) {
		$this->get( 'fos_user.user_manager' )->updateUser( $user, false );
		parent::updateEntity( $user );
	}

	public function prePersistUsuarioEntity( $user ) {
		$this->get( 'fos_user.user_manager' )->updateUser( $user, false );
	}

	public function preUpdateUsuarioEntity( $user ) {
		$this->get( 'fos_user.user_manager' )->updateUser( $user, false );
	}

	public function removeMocionEntity() {

		$em = $this->em;
		// change the properties of the given entity and save the changes
		$id     = $this->request->query->get( 'id' );
		$entity = $em->getRepository( Mocion::class )->find( $id );

		foreach ( $entity->getVotos() as $voto ) {
			$votacion = $voto->getVotacion();
			$voto->setVotacion( null );
			$em->remove( $voto );
			$em->remove( $votacion );
		}

		foreach ( $entity->getVotaciones() as $votacion ) {
			$em->remove( $votacion );
		}

		$em->remove( $entity );


		$this->em->flush();

		// redirect to the 'list' view of the given entity ...
		return $this->redirectToRoute( 'easyadmin',
			[
				'action' => 'list',
				'entity' => $this->request->query->get( 'entity' ),
			] );
	}
}
