<?php

namespace Application\Sonata\UserBundle\Controller;

use FOS\UserBundle\Controller\ResettingController as BaseController;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\DependencyInjection\ContainerAware;

class ResettingController extends BaseController {

	/**
	 * Get the truncated email displayed when requesting the resetting.
	 *
	 * The default implementation only keeps the part following @ in the address.
	 *
	 * @param UserInterface $user
	 *
	 * @return string
	 */
	protected function getObfuscatedEmail(UserInterface $user) {
		return $user->getEmail();
	}

}
