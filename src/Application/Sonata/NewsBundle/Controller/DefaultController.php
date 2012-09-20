<?php

namespace Application\Sonata\NewsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller {

	public function indexAction() {
		$posts = $this->get('sonata.news.manager.post')->getPager(
				array(), 1, 9
		);

		$doctrine = $this->getDoctrine();
		$stats = array(
			'top_countries' => $doctrine->getRepository('EuroCoinBundle:Coin')->findTopCountries(),
		);

		return $this->render('ApplicationSonataNewsBundle:Default:index.html.twig', array(
					'posts' => $posts,
					'stats' => $stats,
				));
	}

}
