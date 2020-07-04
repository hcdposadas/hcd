<?php


namespace App\Exception;


use Symfony\Component\Security\Core\Exception\AccountStatusException;

class AccountDisabledException extends AccountStatusException {

	/**
	 * {@inheritdoc}
	 */
	public function getMessageKey() {
		return 'Account disabled.';
	}
}