<?php

namespace Euro\CoinBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * Country controller.
 */
class CountryController extends Controller {

	/**
	 * Lists all Country entities.
	 */
	public function indexAction() {
		$em = $this->getDoctrine()->getEntityManager();

		return $this->render('EuroCoinBundle:Country:index.html.twig', array(
					'countries' => $em->getRepository('EuroCoinBundle:Country')->findAll(),
				));
	}

	public function menuListAction() {
		$em = $this->getDoctrine()->getEntityManager();

		$countries = $em->getRepository('EuroCoinBundle:Country')->findAll();
		$translator = $this->get('translator');

		usort($countries, function ($a, $b) use ($translator) {
					return strcmp($translator->trans($a->getName()), $translator->trans($b->getName()));
				});

		return $this->render('EuroCoinBundle:Country:menu_list.html.twig', array(
					'countries' => $countries,
				));
	}

	/**
	 * Finds and displays a Country entity.
	 */
	public function showAction($id) {
		$em = $this->getDoctrine()->getEntityManager();
		$country = $em->getRepository('EuroCoinBundle:Country')->find($id);

		$coins = null;
		$counts = array('value' => array(), 'year' => array());
		$values = array();
		if ($country) {
			$coins = $em->getRepository('EuroCoinBundle:Coin')->getCoinsByCountry($country);
			foreach ($coins as $coin) {
				$coin_mintage = $coin->getMintage();
				$coin_year = (string) $coin->getYear();
				$value = $coin->getValue();
				$value_value = sprintf('%.02f', $value->getValue());

				if (!in_array($value, $values)) {
					$values[] = $value;
				}

				if (!isset($counts['value'][$value_value])) {
					$counts['value'][$value_value] = $coin_mintage;
				} else {
					$counts['value'][$value_value] += $coin_mintage;
				}

				if (!isset($counts['year'][$coin_year])) {
					$counts['year'][$coin_year] = $coin_mintage;
				} else {
					$counts['year'][$coin_year] += $coin_mintage;
				}
			}

			krsort($counts['value']);
			rsort($values);
		}

		return $this->render('EuroCoinBundle:Country:show.html.twig', array(
					'coins' => $coins,
					'country' => $country,
					'counts' => array_sum($counts['value']),
					'counts_value' => $counts['value'],
					'counts_year' => $counts['year'],
					'values' => $values,
				));
	}

}
