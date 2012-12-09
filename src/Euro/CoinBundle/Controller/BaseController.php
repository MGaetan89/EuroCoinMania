<?php

namespace Euro\CoinBundle\Controller;

use Euro\CoinBundle\Entity\UserCoin;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

abstract class BaseController extends Controller {

	/**
	 * Build the lists of coins and values for each country in <var>$source</var>
	 * @param array $source The list of coins as retrieve from the database
	 * @param string $order Either 'ASC' or 'DESC' to sort the coins values
	 * @return array The lists of coins and values
	 */
	protected function _buildVars(array $source, $order) {
		$coins = array();
		$translator = $this->get('translator');
		$values = array();

		foreach ($source as $coin) {
			if ($coin instanceof UserCoin) {
				$coin = $coin->getCoin();
			}

			$country = $translator->trans((string) $coin->getCountry());
			$value = (string) $coin->getValue()->getValue();
			$year = (string) $coin->getYear();

			$coins[$country][$year][$value] = $coin;

			if (!isset($values[$country])) {
				$values[$country] = array($value);
			} else if (!in_array($value, $values[$country])) {
				$values[$country][] = $value;
			}
		}

		$sortFct = 'rsort';
		if (strtolower($order) == 'asc') {
			$sortFct = 'sort';
		}

		foreach ($values as &$value) {
			$sortFct($value);
		}

		ksort($coins);

		return array($coins, $values);
	}

}
