<?php

namespace Euro\CoinBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Euro\CoinBundle\Entity\Coin;
use Euro\CoinBundle\Form\CoinType;

/**
 * Coin controller.
 *
 */
class CoinController extends Controller {

	/**
	 * Lists all Coin entities.
	 *
	 */
	public function indexAction($country, $year, $value, $commemorative) {
		$em = $this->getDoctrine()->getEntityManager();
		$translator = $this->get('translator');

		$filters = array(
			'commemorative' => $commemorative,
			'country' => $country,
			'value' => $value,
			'year' => $year,
		);
		$coins = $em->getRepository('EuroCoinBundle:Coin')->getCoinsByFilters($filters);
		$coin_values = array();
		$commemoratives = array();
		$countries = array();
		$values = array();
		$years = array();
		foreach ($coins as $coin) {
			$country = $coin->getCountry();
			$value = $coin->getValue();

			$coin_values[$country->getId()][$value->getId()] = (string) $value;
			$commemoratives[$coin->getCommemorative()] = $coin->getCommemorative();
			$countries[$country->getId()] = $translator->trans($coin->getCountry());
			$values[$value->getId()] = (string) $value;
			$years[$coin->getYear()] = $coin->getYear();
		}

		asort($countries);
		asort($values);
		sort($years);
		foreach ($coin_values as $country => &$val) {
			rsort($val);
		}

		return $this->render('EuroCoinBundle:Coin:index.html.twig', array(
					'coin_values' => $coin_values,
					'coins' => $coins,
					'commemoratives' => count($commemoratives) === 2,
					'countries' => (count($countries) > 1) ? $countries : array(),
					'filters' => $filters,
					'values' => (count($values) > 1) ? $values : array(),
					'years' => (count($years) > 1) ? $years : array(),
				));
	}

	/**
	 * Finds and displays a Coin entity.
	 *
	 */
	public function showAction($id) {
		$em = $this->getDoctrine()->getEntityManager();

		$coin = $em->getRepository('EuroCoinBundle:Coin')->find($id);

		if (!$coin) {
			throw $this->createNotFoundException('Unable to find Coin entity.');
		}

		return $this->render('EuroCoinBundle:Coin:show.html.twig', array(
					'coin' => $coin,
				));
	}

}
