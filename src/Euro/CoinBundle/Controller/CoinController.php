<?php

namespace Euro\CoinBundle\Controller;

use Euro\CoinBundle\Entity\UserCoin;
use Symfony\Component\HttpFoundation\Response;

class CoinController extends BaseController {

	public function addRemoveAction($id, $type) {
		$translator = $this->get('translator');

		if (!$user = $this->getUser()) {
			throw $this->createNotFoundException($translator->trans('user.login_required'));
		}

		$em = $this->getDoctrine()->getManager();
		$uc = $em->getRepository('EuroCoinBundle:UserCoin')->findOneBy(array(
			'coin' => $id,
			'user' => $user,
				));

		if ($type === 'add') {
			$coin = $em->getRepository('EuroCoinBundle:Coin')->find($id);

			if (!$coin) {
				throw $this->createNotFoundException($translator->trans('coin.not_found'));
			}

			if ($coin->getMemberTotal() === $coin->getMintage()) {
				throw $this->createNotFoundException($translator->trans('coin.not_available'));
			}

			if (!$uc) {
				$uc = new UserCoin();
				$uc->setUser($user);
				$uc->setCoin($coin);

				$em->persist($uc);
			} else {
				$uc->addUnit();
			}

			$coin->addUnit();
		} else if ($type === 'remove') {
			if (!$uc || $uc->getQuantity() === 0) {
				throw $this->createNotFoundException($translator->trans('user.coin_not_found'));
			}

			if ($uc->getUser()->getId() !== $user->getId()) {
				throw $this->createNotFoundException($translator->trans('user.not_allowed'));
			}

			$coin = $em->getRepository('EuroCoinBundle:Coin')->find($id);

			if (!$coin) {
				throw $this->createNotFoundException($translator->trans('coin.not_found'));
			}

			$coin->removeUnit();
			$uc->removeUnit();
		}

		$em->flush();

		return new Response($uc->getQuantity());
	}

	public function collectionAction($id) {
		$doctrine = $this->getDoctrine();
		$translator = $this->get('translator');
		$countries = $doctrine->getRepository('EuroCoinBundle:Country')->findBy(array(), array('join_date' => 'ASC'));
		$country = null;

		usort($countries, function ($a, $b) use (&$country, $id, $translator) {
					$a_name = $translator->trans((string) $a->getName());
					$b_name = $translator->trans((string) $b->getName());

					if ($id == $a->getId()) {
						$country = $a;
					}

					return strcmp($a_name, $b_name);
				});

		if (!$country && $countries) {
			$country = $countries[0];

			if (!$id) {
				return $this->redirect($this->generateUrl('coin_collection', array(
									'country' => $translator->trans((string) $country),
									'id' => $country->getId(),
								)));
			}
		}

		$coins = array();
		$values = array();
		$uc = array();

		if ($country) {
			$coins = $doctrine->getRepository('EuroCoinBundle:Coin')->findCoinsByCountry($country);

			list($coins, $values) = $this->_buildVars($coins);

			$coins = array_shift($coins);
			$values = array_shift($values);

			$user_coins = array();
			if ($this->getUser()) {
				$doctrine->getRepository('EuroCoinBundle:UserCoin')->findBy(array('user' => $this->getUser()));

				foreach ($user_coins as $user_coin) {
					if ($user_coin->getQuantity() > 0) {
						$uc[$user_coin->getCoin()->getId()] = $user_coin->getQuantity();
					}
				}
			}
		}

		return $this->render('EuroCoinBundle:Coin:collection.html.twig', array(
					'coins' => $coins,
					'countries' => $countries,
					'current' => $country,
					'uc' => $uc,
					'all_values' => $values,
				));
	}

	public function getAction($id) {
		$coin = $this->getDoctrine()->getRepository('EuroCoinBundle:Coin')->find($id);

		return $this->render('EuroCoinBundle:Coin:coin_data.html.twig', array(
					'coin' => $coin,
				));
	}

}
