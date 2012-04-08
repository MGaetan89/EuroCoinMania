<?php

namespace Euro\CoinBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller {

	public function indexAction($name) {
		return $this->render('EuroCoinBundle:Default:index.html.twig', array('name' => $name));
	}

}
