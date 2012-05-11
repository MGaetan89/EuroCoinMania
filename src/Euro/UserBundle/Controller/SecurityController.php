<?php

namespace Euro\UserBundle\Controller;

use FOS\UserBundle\Controller\SecurityController as Controller;
use Symfony\Component\Security\Core\SecurityContext;

class SecurityController extends Controller {

	public function loginBoxAction() {
		$request = $this->container->get('request');
		$session = $request->getSession();

		// get the error if any (works with forward and redirect -- see below)
		$error = '';
		if ($request->attributes->has(SecurityContext::AUTHENTICATION_ERROR)) {
			$error = $request->attributes->get(SecurityContext::AUTHENTICATION_ERROR);
		} else if (null !== $session && $session->has(SecurityContext::AUTHENTICATION_ERROR)) {
			$error = $session->get(SecurityContext::AUTHENTICATION_ERROR);
			$session->remove(SecurityContext::AUTHENTICATION_ERROR);
		}

		if ($error) {
			// TODO: this is a potential security risk (see http://trac.symfony-project.org/ticket/9523)
			$error = $error->getMessage();
		}

		$lastUsername = ($session === null) ? '' : $session->get(SecurityContext::LAST_USERNAME);
		$csrfToken = $this->container->get('form.csrf_provider')->generateCsrfToken('authenticate');

		return $this->container->get('templating')->renderResponse('FOSUserBundle:Security:login_form.html.' . $this->container->getParameter('fos_user.template.engine'), array(
					'last_username' => $lastUsername,
					'error' => $error,
					'csrf_token' => $csrfToken,
				));
	}

}
