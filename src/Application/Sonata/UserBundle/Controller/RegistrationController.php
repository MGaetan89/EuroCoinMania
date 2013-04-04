<?php

namespace Application\Sonata\UserBundle\Controller;

use FOS\UserBundle\Controller\RegistrationController as BaseController;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\UserEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class RegistrationController extends BaseController {

	public function confirmedAction() {
		$user = $this->container->get('security.context')->getToken()->getUser();

		if (is_object($user) && $user instanceof UserInterface) {
			$this->container->get('session')->getFlashBag()->add('success', 'user.registration_success');
		}

		return new RedirectResponse($this->container->get('router')->generate('welcome'));
	}
}
