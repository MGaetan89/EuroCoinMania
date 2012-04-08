<?php

namespace Euro\CoinBundle\Twig;

use Doctrine\ORM\EntityManager;

class Extension extends \Twig_Extension {
	private $em;

	public function __construct(EntityManager $em) {
		$this->em = $em;
	}

	public function getGlobals() {
		return array(
			'euro_countries' => $this->em->getRepository('EuroCoinBundle:Country')->findBy(array(), array(
				'name' => 'ASC',
			)),
		);
	}

	public function getName() {
		return 'euro_coinbundle_extension';
	}

}
