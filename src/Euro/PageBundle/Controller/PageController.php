<?php

namespace Euro\PageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PageController extends Controller {

	public function copyrightAction() {
		$webmaster = $this->getDoctrine()->getRepository('ApplicationSonataUserBundle:User')->find(1);

		return $this->render('EuroPageBundle:Page:copyright.html.twig', array(
					'webmaster' => $webmaster,
				));
	}

	// @TODO: Used a table to store links
	public function linksAction() {
		return $this->render('EuroPageBundle:Page:links.html.twig');
	}

}
