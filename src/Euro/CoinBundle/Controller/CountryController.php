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

		if (!$country) {
			throw $this->createNotFoundException('Unable to find Country entity.');
		}

		$coins = $em->getRepository('EuroCoinBundle:Coin')->getCoinsByCountry($country);
		$counts = array('value' => array(), 'year' => array());
		foreach ($coins as $coin) {
			if (!isset($counts['value'][(string) $coin->getValue()])) {
				$counts['value'][(string) $coin->getValue()] = $coin->getMintage();
			} else {
				$counts['value'][(string) $coin->getValue()] += $coin->getMintage();
			}

			if (!isset($counts['year'][(string) $coin->getYear()])) {
				$counts['year'][(string) $coin->getYear()] = $coin->getMintage();
			} else {
				$counts['year'][(string) $coin->getYear()] += $coin->getMintage();
			}
		}

		krsort($counts['value']);

		return $this->render('EuroCoinBundle:Country:show.html.twig', array(
					'coins' => $coins,
					'country' => $country,
					'counts' => array_sum($counts['value']),
					'counts_value' => $counts['value'],
					'counts_year' => $counts['year'],
				));
	}

}
