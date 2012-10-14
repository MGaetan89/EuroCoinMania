<?php

namespace Application\Sonata\UserBundle\Controller;

use FOS\UserBundle\Controller\SecurityController as BaseController;

class SecurityController extends BaseController {

	protected function renderLogin(array $data) {
		$tplfile = 'FOSUserBundle:Security:login.html.%s';
		if ($this->container->get('request')->get('_route') === '_internal') {
			$tplfile = 'FOSUserBundle:Security:login_content.html.%s';
		}

		$template = sprintf($tplfile, $this->container->getParameter('fos_user.template.engine'));

		return $this->container->get('templating')->renderResponse($template, $data);
	}

}
