<?php

namespace Euro\CoinBundle\Controller;

use FOS\UserBundle\Model\UserInterface;
use Euro\CoinBundle\Entity\Share;
use Euro\CoinBundle\Entity\UserCoin;
use Euro\PrivateMessageBundle\Entity\PrivateMessage;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

/**
 * Coin controller.
 */
class CoinController extends Controller {

	/**
	 * Lists all Coin entities.
	 */
	public function indexAction($country, $year, $value, $collector) {
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
		$em = $this->getDoctrine()->getEntityManager();
		$coins = $em->getRepository('EuroCoinBundle:Coin')->getCoinsByFilters($db_filters);
		$vars = $this->buildVars($coins);

		return $this->render('EuroCoinBundle:Coin:index.html.twig', array(
					'coin_values' => $vars->coin_values,
					'coins' => $vars->coins,
					'collector' => $collector,
					'commemoratives' => count($vars->commemoratives) === 2,
					'countries' => (count($vars->countries) > 1) ? $vars->countries : array(),
					'filters' => $filters,
					'values' => (count($vars->values) > 1) ? $vars->values : array(),
					'years' => (count($vars->years) > 1) ? $vars->years : array(),
				));
	}

	/**
	 * Finds and displays a Coin entity.
	 */
	public function showAction($id) {
		$em = $this->getDoctrine()->getEntityManager();

		return $this->render('EuroCoinBundle:Coin:show.html.twig', array(
					'coin' => $em->getRepository('EuroCoinBundle:Coin')->find($id),
				));
	}

	public function getAction($id) {
		if (!$this->getUser() instanceof UserInterface) {
			throw new \Exception('You are not allowed to access this page !');
		}

		$em = $this->getDoctrine()->getEntityManager();

		return $this->render('EuroCoinBundle:Coin:popover.html.twig', array(
					'coin' => $em->getRepository('EuroCoinBundle:Coin')->find($id),
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

		if ($user->getId() !== $uc->getUser()->getId()) {
			throw new \Exception('You are not allowed to access this page !');
		}

		if ($uc->getQuantity() > 0) {
			$coin = $uc->getCoin();
			$coin->setMemberTotal($coin->getMemberTotal() - 1);

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
		$update = false;
		$uc = null;
		if ($id[0] !== 'c') {
			$uc = $em->getRepository('EuroCoinBundle:UserCoin')->find($id);
		}

		if ($uc) {
			if ($user->getId() !== $uc->getUser()->getId()) {
				throw new \Exception('You are not allowed to access this page !');
			}

			$coin = $uc->getCoin();
			if ($coin->getMemberTotal() < $coin->getMintage()) {
				$uc->setQuantity($uc->getQuantity() + 1);
				$update = true;
			}
		} else {
			$coin = $em->getRepository('EuroCoinBundle:Coin')->find(substr($id, 1));

			if (!$coin) {
				throw $this->createNotFoundException('Unable to find Coin entity.');
			}

			if ($coin->getMemberTotal() < $coin->getMintage()) {
				$uc = new UserCoin();
				$uc->setUser($user);
				$uc->setCoin($coin);

				$em->persist($uc);
				$update = true;
			}
		}

		if ($update) {
			$coin->setMemberTotal($coin->getMemberTotal() + 1);
			$em->flush();
		}

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

		$from_uc = array();
		foreach ($uc as $ucoin) {
			$from_uc[$ucoin->getCoin()->getId()] = $ucoin;
		}

		return $this->render('EuroCoinBundle:Coin:doubles.html.twig', array(
					'coin_values' => $vars->coin_values,
					'coins' => $vars->coins,
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
		if ($from->getUser()->getId() !== $user->getId()) {
			throw new \Exception('You are not allowed to access this page !');
		}

		$doubles = $em->getRepository('EuroCoinBundle:UserCoin')->getDifferentDoublesByUserAndCoin($user, $from->getCoin()->getId());
		$vars = $this->buildVars($doubles);

		$from_uc = array();
		foreach ($doubles as $double) {
			$from_uc[$double->getCoin()->getId()] = $double;
		}

		return $this->render('EuroCoinBundle:Coin:doubles_share.html.twig', array(
					'from' => $from,
					'coin_values' => $vars->coin_values,
					'coins' => $vars->coins,
					'commemoratives' => count($vars->commemoratives) === 2,
					'countries' => (count($vars->countries) > 1) ? $vars->countries : array(),
					'uc' => $from_uc,
					'values' => (count($vars->values) > 1) ? $vars->values : array(),
					'years' => (count($vars->years) > 1) ? $vars->years : array(),
				));
	}

	public function doublesShareAcceptAction($id) {
		$user = $this->getUser();
		if (!$user instanceof UserInterface) {
			throw new \Exception('You are not allowed to access this page !');
		}

		$em = $this->getDoctrine()->getEntityManager();
		$repository = $em->getRepository('EuroCoinBundle:Share');
		if ($repository->find($id)->getToUserCoin()->getUser()->getId() !== $user->getId()) {
			throw new \Exception('You are not allowed to access this page !');
		}

		$repository->setAccepted($id);

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
		$user_id = $user->getId();

		if ($from->getUser()->getId() !== $user_id && $to->getUser()->getId() !== $user_id) {
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

		return $this->render('EuroCoinBundle:Coin:shares.html.twig', array(
					'shares' => $em->getRepository('EuroCoinBundle:Share')->getSharesByUser($user),
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

			$coin_values[$country->getId()][$value->getId()] = $value;
			$commemoratives[$coin->getCommemorative()] = $coin->getCommemorative();
			$countries[$country->getId()] = $translator->trans($country);
			$sorted[$countries[$country->getId()]][] = $coin;
			$values[$value->getId()] = $value;
			$years[$year->getId()] = $year;
		}

		asort($countries);
		asort($values);
		asort($years);
		ksort($sorted);
		foreach ($coin_values as $country => &$val) {
			usort($val, function ($a, $b) {
						$a = $a->getValue();
						$b = $b->getValue();
						if ($a < $b) {
							return 1;
						}

						if ($a > $b) {
							return -1;
						}

						return 0;
					});
		}

		$coins = array();
		foreach ($sorted as $country) {
			$coins = array_merge($coins, $country);
		}

		return (object) array(
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
