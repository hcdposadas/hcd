<?php

namespace AppBundle\Controller;

use JavierEguiluz\Bundle\EasyAdminBundle\Controller\AdminController as BaseAdminController;

class AdminController extends BaseAdminController {
	public function createNewUsuarioEntity() {
		return $this->get( 'fos_user.user_manager' )->createUser();
	}

	public function updateUsuarioEntity($user)
	{
		$this->get('fos_user.user_manager')->updateUser($user, false);
		parent::updateUsuarioEntity($user);
	}

	public function prePersistUsuarioEntity( $user ) {
		$this->get( 'fos_user.user_manager' )->updateUser( $user, false );
	}

	public function preUpdateUsuarioEntity( $user ) {
		$this->get( 'fos_user.user_manager' )->updateUser( $user, false );
	}
}
