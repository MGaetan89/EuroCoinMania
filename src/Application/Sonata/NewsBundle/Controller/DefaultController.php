<?php

namespace Application\Sonata\NewsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller {

	public function indexAction() {
		$doctrine = $this->getDoctrine();
		$uc_repo = $doctrine->getRepository('EuroCoinBundle:UserCoin');

		return $this->render('ApplicationSonataNewsBundle:Default:index.html.twig', array(
			'posts' => $this->get('sonata.news.manager.post')->getPager(array(), 1, 5),
			'stats' => array(
				'biggest_collection' => $uc_repo->findBiggestCollectionStats(),
				'most_valued_collection' => $uc_repo->findMostValuedCollectionStats(),
				'top_countries' => $doctrine->getRepository('EuroCoinBundle:Coin')->findTopCountries(),
			),
		));
	}

}
