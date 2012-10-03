<?php

namespace Application\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PageController extends Controller {

	public function copyrightAction() {
		$webmaster = $this->getDoctrine()->getRepository('ApplicationSonataUserBundle:User')->find(1);

		return $this->render('ApplicationPageBundle:Page:copyright.html.twig', array(
					'webmaster' => $webmaster,
				));
	}

	// @TODO: Used a table to store links
	public function linksAction() {
		return $this->render('ApplicationPageBundle:Page:links.html.twig');
	}

}
