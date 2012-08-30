<?php

namespace Euro\CoinBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller {

	public function indexAction() {
		$posts = $this->get('sonata.news.manager.post')->getPager(
				array(), 1, 9
		);

		return $this->render('EuroCoinBundle:Default:index.html.twig', array(
			'posts' => $posts,
		));
	}

}
