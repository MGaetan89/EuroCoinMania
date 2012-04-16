<?php

namespace Euro\CoinBundle\Twig;

use Doctrine\ORM\EntityManager;
use Symfony\Component\Translation\TranslatorInterface;

class Extension extends \Twig_Extension {
	private $em;
	private $translator;

	public function __construct(EntityManager $em, TranslatorInterface $translator) {
		$this->em = $em;
		$this->translator = $translator;
	}

	public function getFilters() {
		return array(
			'reset' => new \Twig_Filter_Method($this, 'resetFilter'),
		);
	}

	public function getGlobals() {
		$countries = $this->em->getRepository('EuroCoinBundle:Country')->findAll();
		$translator = $this->translator;

		usort($countries, function ($a, $b) use ($translator) {
					return strcmp($translator->trans($a->getName()), $translator->trans($b->getName()));
				});

		return array(
			'euro_countries' => $countries,
		);
	}

	public function getName() {
		return 'euro_coinbundle_extension';
	}

	public function resetFilter(array $array, $key) {
		if (isset($array[$key])) {
			$array[$key] = '';
		}

		return $array;
	}

}
