<?php

namespace Euro\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller {

	public function indexAction($name) {
		return $this->render('EuroUserBundle:Default:index.html.twig', array('name' => $name));
	}

}
