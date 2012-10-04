<?php

namespace Euro\ContactBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ContactController extends Controller {

	public function contactAction() {
		return $this->render('EuroContactBundle:Contact:index.html.twig');
	}

}
