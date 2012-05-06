<?php

namespace Euro\CoinBundle\Twig;

class Extension extends \Twig_Extension {
	private $csrf_provider;

	public function __construct($csrf_provider) {
		$this->csrf_provider = $csrf_provider;
	}

	public function getFilters() {
		return array(
			'reset' => new \Twig_Filter_Method($this, 'resetFilter'),
		);
	}

	public function getGlobals() {
		return array(
			'csrf_token' => $this->csrf_provider->generateCsrfToken('authenticate'),
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
