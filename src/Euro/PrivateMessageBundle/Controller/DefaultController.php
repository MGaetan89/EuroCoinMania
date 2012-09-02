<?php

namespace Euro\PrivateMessageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller {

	public function indexAction($name) {
		return $this->render('EuroPrivateMessageBundle:Default:index.html.twig', array('name' => $name));
	}

}
