<?php

namespace Euro\CoinBundle\Twig;

class Extension extends \Twig_Extension {

	public function getFilters() {
		return array(
			'reset' => new \Twig_Filter_Method($this, 'resetFilter'),
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
