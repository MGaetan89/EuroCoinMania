<?php

namespace Application\Sonata\UserBundle\Controller;

use FOS\UserBundle\Controller\RegistrationController as BaseController;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class RegistrationController extends BaseController {

	public function confirmedAction() {
		$user = $this->container->get('security.context')->getToken()->getUser();

        if (is_object($user) && $user instanceof UserInterface) {
			$this->container->get('session')->getFlashBag()->add('success', 'user.registration_success');
		}

		return new RedirectResponse($this->container->get('router')->generate('welcome'));
	}

	public function registerAction() {
		$form = $this->container->get('fos_user.registration.form');
		$formHandler = $this->container->get('fos_user.registration.form.handler');
		$confirmationEnabled = $this->container->getParameter('fos_user.registration.confirmation.enabled');

		$process = $formHandler->process($confirmationEnabled);
		if ($process) {
			$user = $form->getData();

			$authUser = false;
			if ($confirmationEnabled) {
				$this->container->get('session')->set('fos_user_send_confirmation_email/email', $user->getEmail());
				$route = 'fos_user_registration_check_email';
			} else {
				$authUser = true;
				$route = 'fos_user_registration_confirmed';
			}

			$this->setFlash('fos_user_success', 'registration.flash.user_created');
			$url = $this->container->get('router')->generate($route);
			$response = new RedirectResponse($url);

			if ($authUser) {
				$this->authenticateUser($user, $response);
			}

			return $response;
		}

		$tplfile = 'FOSUserBundle:Registration:register.html.';
		if ($this->container->get('request')->get('_route') === '_internal') {
			$tplfile = 'FOSUserBundle:Registration:register_content.html.';
		}

		return $this->container->get('templating')->renderResponse($tplfile . $this->getEngine(), array(
					'form' => $form->createView(),
				));
	}

}
