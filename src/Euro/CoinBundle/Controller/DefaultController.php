<?php

namespace Euro\CoinBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller {

	public function indexAction() {
		return $this->render('EuroCoinBundle:Default:index.html.twig');
	}

}
