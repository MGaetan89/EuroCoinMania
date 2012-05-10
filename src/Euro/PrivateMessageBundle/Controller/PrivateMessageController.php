<?php

namespace Euro\PrivateMessageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PrivateMessageController extends Controller {

	public function indexAction($name) {
		return $this->render('EuroPrivateMessageBundle:PrivateMessage:index.html.twig', array('name' => $name));
	}

}
