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
		$countries = array();
		$translator = $this->get('translator');
		$values = array();
		$years = array();

		foreach ($source as $coin) {
			if ($coin instanceof UserCoin) {
				$coin = $coin->getCoin();
			}

			$country = $coin->getCountry();
			$countryStr = $translator->trans((string) $country);
			$value = (string) $coin->getValue()->getValue();
			$year = $coin->getYear();
			$yearStr = (string) $year;
			$yearShort = substr($yearStr, 0, 4);

			$coins[$countryStr][$yearStr][$value] = $coin;

			if (!isset($countries[$countryStr])) {
				$countries[$countryStr] = $country;
			}

			if (!isset($values[$countryStr])) {
				$values[$countryStr] = array($value);
			} else if (!in_array($value, $values[$countryStr])) {
				$values[$countryStr][] = $value;
			}

			if (!isset($years[$yearShort])) {
				$years[$yearShort] = $year;
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
		ksort($countries);

		return array($coins, $values, $years, $countries);
	}

}
