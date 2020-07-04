<?php

namespace App\Security;

use App\Entity\Usuario;
use App\Exception\AccountDeletedException;
use App\Exception\AccountDisabledException;
use Symfony\Component\Security\Core\User\UserCheckerInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class UserChecker implements UserCheckerInterface {
	public function checkPreAuth( UserInterface $user ) {
		if ( ! $user instanceof Usuario ) {
			return;
		}

		// user is deleted, show a generic Account Not Found message.
		if ( ! $user->getEnabled() ) {
			throw new AccountDisabledException( 'Usuario no habilitado' );
		}
	}

	public function checkPostAuth( UserInterface $user ) {
		if ( ! $user instanceof Usuario ) {
			return;
		}

		// user account is expired, the user may be notified
//		if ($user->isExpired()) {
//			throw new AccountExpiredException('...');
//		}
	}
}