<?php

namespace Euro\CoinBundle\Controller;

use FOS\UserBundle\Model\UserInterface;
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
		$vars = $this->buildVars($coins);

		return $this->render('EuroCoinBundle:Coin:index.html.twig', array(
					'coin_values' => $vars['coin_values'],
					'coins' => $vars['coins'],
					'collector' => $collector,
					'commemoratives' => count($vars['commemoratives']) === 2,
					'countries' => (count($vars['countries']) > 1) ? $vars['countries'] : array(),
					'filters' => $filters,
					'values' => (count($vars['values']) > 1) ? $vars['values'] : array(),
					'years' => (count($vars['years']) > 1) ? $vars['years'] : array(),
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
		if (!$this->getUser() instanceof UserInterface) {
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
		if (!$user instanceof UserInterface) {
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
		if (!$user instanceof UserInterface) {
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
		if (!$user instanceof UserInterface) {
			throw new \Exception('You are not allowed to access this page !');
		}

		$em = $this->getDoctrine()->getEntityManager();
		$uc = $em->getRepository('EuroCoinBundle:UserCoin')->getDoublesByUser($user);
		$vars = $this->buildVars($uc);

		$quantity = array();
		foreach ($uc as $ucoin) {
			$quantity[$ucoin->getCoin()->getId()] = $ucoin->getQuantity();
		}

		return $this->render('EuroCoinBundle:Coin:doubles.html.twig', array(
					'coin_values' => $vars['coin_values'],
					'coins' => $vars['coins'],
					'quantity' => $quantity,
				));
	}

	public function doublesShareAction($id) {
		$user = $this->getUser();
		if (!$user instanceof UserInterface) {
			throw new \Exception('You are not allowed to access this page !');
		}

		$em = $this->getDoctrine()->getEntityManager();
		$doubles = $em->getRepository('EuroCoinBundle:UserCoin')->getDifferentDoublesByUserAndCoin($user, $id);

		$vars = $this->buildVars($doubles);

		$users = array();
		foreach ($doubles as $double) {
			$users[$double->getCoin()->getId()] = $double->getUser();
		}

		return $this->render('EuroCoinBundle:Coin:doubles_share.html.twig', array(
					'coin_values' => $vars['coin_values'],
					'coins' => $vars['coins'],
					'commemoratives' => count($vars['commemoratives']) === 2,
					'countries' => (count($vars['countries']) > 1) ? $vars['countries'] : array(),
					'users' => $users,
					'values' => (count($vars['values']) > 1) ? $vars['values'] : array(),
					'years' => (count($vars['years']) > 1) ? $vars['years'] : array(),
				));
	}

	private function buildVars(array $coins) {
		$translator = $this->get('translator');

		$coin_values = array();
		$commemoratives = array();
		$countries = array();
		$sorted = array();
		$values = array();
		$years = array();
		foreach ($coins as $coin) {
			if ($coin instanceof UserCoin) {
				$coin = $coin->getCoin();
			}

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

		return array(
			'coin_values' => $coin_values,
			'coins' => $coins,
			'commemoratives' => $commemoratives,
			'countries' => $countries,
			'sorted' => $sorted,
			'values' => $values,
			'years' => $years,
		);
	}

	private function getUser() {
		return $this->get('security.context')->getToken()->getUser();
	}

}
