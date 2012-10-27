<?php

namespace Application\Sonata\NewsBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller {

	public function indexAction() {
		$posts = $this->get('sonata.news.manager.post')->getPager(
				array(), 1, 6
		);

		$doctrine = $this->getDoctrine();
		$uc_repo = $doctrine->getRepository('EuroCoinBundle:UserCoin');
		$stats = array(
			'biggest_collections' => $uc_repo->findBiggestCollections(),
			'most_valued_collections' => $uc_repo->findMostValuedCollections(),
			'top_countries' => $doctrine->getRepository('EuroCoinBundle:Coin')->findTopCountries(),
		);

		return $this->render('ApplicationSonataNewsBundle:Default:index.html.twig', array(
					'posts' => $posts,
					'stats' => $stats,
				));
	}

}
