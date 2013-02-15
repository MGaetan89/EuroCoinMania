<?php

namespace Application\Sonata\NewsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller {

	public function indexAction() {
		$doctrine = $this->getDoctrine();

		return $this->render('ApplicationSonataNewsBundle:Default:index.html.twig', array(
					'posts' => $this->get('sonata.news.manager.post')->getPager(array(), 1, 5),
					'stats' => array(
						'collection' => $doctrine->getRepository('EuroCoinBundle:UserCoin')->findCollectionStats(),
						'top_countries' => $doctrine->getRepository('EuroCoinBundle:Coin')->findTopCountries(),
					),
				));
	}

}

