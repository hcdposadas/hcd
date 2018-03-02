<?php

namespace AppBundle\Controller;

use EasyCorp\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;

class AdminController extends BaseAdminController {
	public function createNewUsuarioEntity() {
		return $this->get( 'fos_user.user_manager' )->createUser();
	}

	public function updateUsuarioEntity($user)
	{
		$this->get('fos_user.user_manager')->updateUser($user, false);
		parent::updateEntity($user);
	}

	public function prePersistUsuarioEntity( $user ) {
		$this->get( 'fos_user.user_manager' )->updateUser( $user, false );
	}

	public function preUpdateUsuarioEntity( $user ) {
		$this->get( 'fos_user.user_manager' )->updateUser( $user, false );
	}
}
