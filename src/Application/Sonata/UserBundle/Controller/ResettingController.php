<?php

namespace Application\Sonata\UserBundle\Controller;

use FOS\UserBundle\Controller\ResettingController as BaseController;
use FOS\UserBundle\Model\UserInterface;

class ResettingController extends BaseController {

	/**
	 * {@inheritDoc}
	 */
	protected function getObfuscatedEmail(UserInterface $user) {
		return $user->getEmail();
	}

}
