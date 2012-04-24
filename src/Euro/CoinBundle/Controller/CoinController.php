<?php

namespace Euro\CoinBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Euro\CoinBundle\Entity\UserCoin;

/**
 * Coin controller.
 *
 */
class CoinController extends Controller {

	/**
	 * Lists all Coin entities.
	 *
	 */
	public function indexAction($country, $year, $value, $commemorative, $collector) {
		$em = $this->getDoctrine()->getEntityManager();
		$translator = $this->get('translator');

		$filters = array(
			'commemorative' => $commemorative,
			'country' => $country,
			'value' => $value,
			'year' => $year,
		);
		$db_filters = array(
			'c.commemorative' => $commemorative,
			'c.country' => $country,
			'c.value' => $value,
			'c.year' => $year,
			'v.collector' => $collector,
		);
		$coins = $em->getRepository('EuroCoinBundle:Coin')->getCoinsByFilters($db_filters);
		$coin_values = array();
		$commemoratives = array();
		$countries = array();
		$sorted = array();
		$values = array();
		$years = array();
		foreach ($coins as $coin) {
			$country = $coin->getCountry();
			$value = $coin->getValue();

			$coin_values[$country->getId()][$value->getId()] = (string) $value;
			$commemoratives[$coin->getCommemorative()] = $coin->getCommemorative();
			$countries[$country->getId()] = $translator->trans($coin->getCountry());
			$sorted[$countries[$country->getId()]][] = $coin;
			$values[$value->getId()] = (string) $value;
			$years[$coin->getYear()] = $coin->getYear();
		}

		asort($countries);
		asort($values);
		ksort($sorted);
		sort($years);
		foreach ($coin_values as $country => &$val) {
			rsort($val);
		}

		$coins = array();
		foreach ($sorted as $country) {
			$coins = array_merge($coins, $country);
		}

		return $this->render('EuroCoinBundle:Coin:index.html.twig', array(
					'coin_values' => $coin_values,
					'coins' => $coins,
					'collector' => $collector,
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

	public function getAction($id) {
		if (!$this->getUser() instanceof \FOS\UserBundle\Model\UserInterface) {
			throw new \Exception('You are not allowed to access this page !');
		}

		$em = $this->getDoctrine()->getEntityManager();

		$coin = $em->getRepository('EuroCoinBundle:Coin')->find($id);

		if (!$coin) {
			throw $this->createNotFoundException('Unable to find Coin entity.');
		}

		return $this->render('EuroCoinBundle:Coin:popover.html.twig', array(
					'coin' => $coin,
					'popover' => true,
				));
	}

	public function removeAction($id) {
		$user = $this->getUser();
		if (!$user instanceof \FOS\UserBundle\Model\UserInterface) {
			throw new \Exception('You are not allowed to access this page !');
		}

		$em = $this->getDoctrine()->getEntityManager();
		$uc = $em->getRepository('EuroCoinBundle:UserCoin')->find($id);

		if (!$uc) {
			throw $this->createNotFoundException('Unable to find UserCoin entity.');
		}

		if ($user != $uc->getUser()) {
			throw new \Exception('You are not allowed to access this page !');
		}

		if ($uc->getQuantity() > 0) {
			$uc->setQuantity($uc->getQuantity() - 1);
			$em->flush();
		}

		return new Response($uc->getQuantity());
	}

	public function addAction($id) {
		$user = $this->getUser();
		if (!$user instanceof \FOS\UserBundle\Model\UserInterface) {
			throw new \Exception('You are not allowed to access this page !');
		}

		$em = $this->getDoctrine()->getEntityManager();
		$uc = null;
		if ($id[0] !== 'c') {
			$uc = $em->getRepository('EuroCoinBundle:UserCoin')->find($id);
		}

		if (!$uc) {
			$coin = $em->getRepository('EuroCoinBundle:Coin')->find(substr($id, 1));

			if (!$coin) {
				throw $this->createNotFoundException('Unable to find Coin entity.');
			}

			$uc = new UserCoin();
			$uc->setUser($user);
			$uc->setCoin($coin);
			$uc->setQuantity(1);

			$em->persist($uc);
		} else {
			if ($user != $uc->getUser()) {
				throw new \Exception('You are not allowed to access this page !');
			}

			$uc->setQuantity($uc->getQuantity() + 1);
		}

		$em->flush();

		return new Response($uc->getQuantity());
	}

	public function doublesAction() {
		$user = $this->getUser();
		if (!$user instanceof \FOS\UserBundle\Model\UserInterface) {
			throw new \Exception('You are not allowed to access this page !');
		}

		$em = $this->getDoctrine()->getEntityManager();
		$uc = $em->getRepository('EuroCoinBundle:UserCoin')->getDoublesByUser($user);

		$coin_values = array();
		foreach ($uc as $coin) {
			$coin = $coin->getCoin();
			$country = $coin->getCountry();
			$value = $coin->getValue();

			$coin_values[$country->getId()][$value->getId()] = (string) $value;
		}

		foreach ($coin_values as $country => &$cv) {
			rsort($cv);
		}

		return $this->render('EuroCoinBundle:Coin:doubles.html.twig', array(
					'coins' => $uc,
					'coin_values' => $coin_values,
				));
	}

	public function doublesShareAction($id) {
		var_dump($id);
	}

	private function getUser() {
		return $this->get('security.context')->getToken()->getUser();
	}

}
