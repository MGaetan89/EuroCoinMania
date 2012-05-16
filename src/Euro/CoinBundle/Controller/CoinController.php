<?php

namespace Euro\CoinBundle\Controller;

use FOS\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;
use Euro\CoinBundle\Entity\Share;
use Euro\CoinBundle\Entity\UserCoin;
use Euro\PrivateMessageBundle\Entity\PrivateMessage;

/**
 * Coin controller.
 */
class CoinController extends Controller {

	/**
	 * Lists all Coin entities.
	 */
	public function indexAction($country, $year, $value, $collector) {
		$em = $this->getDoctrine()->getEntityManager();

		$filters = array(
			'commemorative' => $collector,
			'country' => $country,
			'value' => $value,
			'year' => $year,
		);
		$db_filters = array(
			'c.commemorative' => $collector,
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
		$from_uc = array();
		foreach ($uc as $ucoin) {
			$coin_id = $ucoin->getCoin()->getId();
			$quantity[$coin_id] = $ucoin->getQuantity();
			$from_uc[$coin_id] = $ucoin;
		}

		return $this->render('EuroCoinBundle:Coin:doubles.html.twig', array(
					'coin_values' => $vars['coin_values'],
					'coins' => $vars['coins'],
					'quantity' => $quantity,
					'uc' => $from_uc,
				));
	}

	public function doublesShareAction($id) {
		$user = $this->getUser();
		if (!$user instanceof UserInterface) {
			throw new \Exception('You are not allowed to access this page !');
		}

		$em = $this->getDoctrine()->getEntityManager();
		$from = $em->getRepository('EuroCoinBundle:UserCoin')->find($id);
		$doubles = $em->getRepository('EuroCoinBundle:UserCoin')->getDifferentDoublesByUserAndCoin($user, $from->getCoin()->getId());
		$vars = $this->buildVars($doubles);

		$users = array();
		$from_uc = array();
		foreach ($doubles as $double) {
			$coin_id = $double->getCoin()->getId();
			$users[$coin_id] = $double->getUser();
			$from_uc[$coin_id] = $double;
		}

		return $this->render('EuroCoinBundle:Coin:doubles_share.html.twig', array(
					'from' => $from,
					'coin_values' => $vars['coin_values'],
					'coins' => $vars['coins'],
					'commemoratives' => count($vars['commemoratives']) === 2,
					'countries' => (count($vars['countries']) > 1) ? $vars['countries'] : array(),
					'uc' => $from_uc,
					'users' => $users,
					'values' => (count($vars['values']) > 1) ? $vars['values'] : array(),
					'years' => (count($vars['years']) > 1) ? $vars['years'] : array(),
				));
	}

	public function doublesShareAcceptAction($id) {
		$user = $this->getUser();
		if (!$user instanceof UserInterface) {
			throw new \Exception('You are not allowed to access this page !');
		}

		$em = $this->getDoctrine()->getEntityManager();
		$em->getRepository('EuroCoinBundle:Share')->setAccepted($id);

		return $this->redirect($this->generateUrl('coin_shares'));
	}

	public function doublesShareProposeAction($from, $to) {
		$user = $this->getUser();
		if (!$user instanceof UserInterface) {
			throw new \Exception('You are not allowed to access this page !');
		}

		$em = $this->getDoctrine()->getEntityManager();
		$repository = $em->getRepository('EuroCoinBundle:UserCoin');
		$translator = $this->get('translator');
		$from = $repository->find($from);
		$to = $repository->find($to);

		if ($from->getUser() !== $user && $to->getUser() !== $user) {
			throw new \Exception('You are not allowed to access this page !');
		}

		if ($from->getQuantity() < 2 || $to->getQuantity() < 2) {
			throw new \Exception('The quantity does not allow a share !');
		}

		// Create conversation
		$from_coin = $from->getCoin();
		$to_coin = $to->getCoin();
		$pm = new PrivateMessage();
		$pm
				->setFromUser($from->getUser())
				->setToUser($to->getUser())
				->setTitle($translator->trans('coin.shares.pm.title', array(
							'%name%' => $from->getUser()->getUsername()
						)))
				->setText($translator->trans('coin.shares.pm.text', array(
							'%from.country%' => $translator->trans($from_coin->getCountry()),
							'%from.name%' => $from->getUser()->getUsername(),
							'%from.value%' => (string) $from_coin->getValue(),
							'%from.year%' => (string) $from_coin->getYear(),
							'%to.country%' => $translator->trans($to_coin->getCountry()),
							'%to.name%' => $to->getUser()->getUsername(),
							'%to.value%' => (string) $to_coin->getValue(),
							'%to.year%' => (string) $to_coin->getYear(),
						)));

		// Register share
		$share = new Share();
		$share
				->setFromUserCoin($from)
				->setToUserCoin($to)
				->setPm($pm);

		$em->persist($pm);
		$em->persist($share);
		$em->flush();

		return $this->redirect($this->generateUrl('coin_shares'));
	}

	public function sharesAction() {
		$user = $this->getUser();
		if (!$user instanceof UserInterface) {
			throw new \Exception('You are not allowed to access this page !');
		}

		$em = $this->getDoctrine()->getEntityManager();
		$shares = $em->getRepository('EuroCoinBundle:Share')->getSharesByUser($user);

		return $this->render('EuroCoinBundle:Coin:shares.html.twig', array(
					'shares' => $shares,
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
			$year = $coin->getYear();

			$coin_values[$country->getId()][$value->getId()] = (string) $value;
			$commemoratives[$coin->getCommemorative()] = $coin->getCommemorative();
			$countries[$country->getId()] = $translator->trans($country);
			$sorted[$countries[$country->getId()]][] = $coin;
			$values[$value->getId()] = (string) $value;
			$years[$year->getId()] = $year;
		}

		asort($countries);
		asort($values);
		asort($years);
		ksort($sorted);
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
