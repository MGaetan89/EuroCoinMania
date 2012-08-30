<?php

namespace Euro\CoinBundle\Controller;

use Euro\CoinBundle\Entity\UserCoin;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

abstract class BaseController extends Controller {

	/**
	 * Build the lists of coins and values for each country in <var>$_coins</var>
	 * @param array $_coins The list of coins as retrieve from the database
	 * @return array The lists of coins and values
	 */
	protected function _buildVars(array $_coins) {
		$coins = array();
		$translator = $this->get('translator');
		$values = array();

		foreach ($_coins as $coin) {
			if ($coin instanceof UserCoin) {
				$coin = $coin->getCoin();
			}

			$country = $translator->trans((string) $coin->getCountry());
			$value = (string) $coin->getValue()->getValue();
			$year = (string) $coin->getYear();

			$coins[$country][$year][$value] = $coin;

			if (!isset($values[$country])) {
				$values[$country] = array();
			}

			if (!in_array($value, $values[$country])) {
				$values[$country][] = $value;
				rsort($values[$country]);
			}
		}

		ksort($coins);

		return array($coins, $values);
	}

}
