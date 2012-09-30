<?php

namespace Euro\CoinBundle\Controller;

use Euro\CoinBundle\Entity\Coin;
use Euro\CoinBundle\Entity\UserCoin;
use Symfony\Component\HttpFoundation\Response;

class CoinController extends BaseController {

	const YEAR_RANGE_SIZE = 7;

	/**
	 * Add or remove one coin in the user collection
	 * @param integer $id The coin id to add/remove in the collection
	 * @param string $type The action to perform (either 'add' or 'remove')
	 * @return Response
	 * @throws Symfony\Component\HttpKernel\Exception\NotFoundHttpException 
	 */
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

	/**
	 * Display all the coins available for a given country
	 * @param integer $id The country id for the collection to display
	 * @param integer $type The type of collection to show
	 * @param integer $year The year to display
	 * @return Response 
	 */
	public function collectionAction($id, $type, $year) {
		$doctrine = $this->getDoctrine();
		$translator = $this->get('translator');
		$countries = $doctrine->getRepository('EuroCoinBundle:Country')->findAll();
		$country = null;

		// Sort the countries by translated name
		usort($countries, function ($a, $b) use (&$country, $id, $translator) {
					$a_name = $translator->trans((string) $a);
					$b_name = $translator->trans((string) $b);

					if ($id == $a->getId()) {
						$country = $a;
					} else if ($id == $b->getId()) {
						$country = $b;
					}

					return strcmp($a_name, $b_name);
				});

		if (!$country && $countries) {
			$country = $countries[0];

			return $this->redirect($this->generateUrl('coin_collection' . $type, array(
								'country' => $translator->trans((string) $country),
								'id' => $country->getId(),
							)));
		}

		$coins = array();
		$totals = array();
		$uc = array();
		$values = array();
		$years = array();

		if ($country) {
			$coin_repo = $doctrine->getRepository('EuroCoinBundle:Coin');
			$years = $coin_repo->findYearsForCountry($country, $type);

			foreach ($years as &$item) {
				$item = $item->getYear();
			}

			$years = array_chunk($years, self::YEAR_RANGE_SIZE);

			foreach ($years as &$item) {
				$size = count($item);
				if ($size > 1) {
					$item = array(
						'from' => $item[0]->getYear(),
						'to' => $item[$size - 1]->getYear(),
					);
				} else {
					$item = array(
						'from' => $item[0]->getYear(),
						'to' => '',
					);
				}
			}

			if (!$year && $years) {
				$year_param = $years[0]['from'];
				if ($years[0]['to']) {
					$year_param .= '..' . $years[0]['to'];
				}

				return $this->redirect($this->generateUrl('coin_collection' . $type, array(
									'country' => $translator->trans((string) $country),
									'id' => $country->getId(),
									'year' => $year_param,
								)));
			}

			$year_range = array_map('intval', explode('..', $year));
			$coins = $coin_repo->findCoinsByCountry($country, $type, $year_range);

			if ($type == Coin::TYPE_CIRCULATION) {
				foreach ($coins as $coin) {
					$value = (string) $coin->getValue()->getValue();

					if (!isset($totals[$value])) {
						$totals[$value] = $coin->getMintage();
					} else {
						$totals[$value] += $coin->getMintage();
					}
				}

				list($coins, $values) = $this->_buildVars($coins);

				$coins = array_shift($coins);
				$values = array_shift($values);
			}

			if ($user = $this->getUser()) {
				$user_coins = $doctrine->getRepository('EuroCoinBundle:UserCoin')->findByCountryForUser($user, $country);

				foreach ($user_coins as $user_coin) {
					if ($user_coin->getQuantity() > 0) {
						$uc[$user_coin->getCoin()->getId()] = $user_coin;
					}
				}
			}
		}

		$tpl_suffix = ($type != Coin::TYPE_CIRCULATION) ? '_collector' : '';

		return $this->render('EuroCoinBundle:Coin:collection' . $tpl_suffix . '.html.twig', array(
					'all_values' => $values,
					'coins' => $coins,
					'type' => $type,
					'countries' => $countries,
					'current' => $country,
					'current_year' => $year,
					'totals' => $totals,
					'uc' => $uc,
					'years' => $years,
				));
	}

	/**
	 * Retrieve data for a given coin
	 * @param integer $id The coin id
	 * @return Response 
	 */
	public function getAction($id) {
		return $this->render('EuroCoinBundle:Coin:coin_data.html.twig', array(
					'coin' => $this->getDoctrine()->getRepository('EuroCoinBundle:Coin')->find($id),
				));
	}

}
