<?php

namespace Euro\CoinBundle\Controller;

use Euro\CoinBundle\Entity\Coin;
use Euro\CoinBundle\Entity\UserCoin;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Locale\Locale;

class CoinController extends BaseController {
	const YEAR_RANGE_SIZE = 7;

	/**
	 * Add or remove one coin in the user collection
	 * @param integer $id The coin id to add/remove in the collection
	 * @param string $type The action to perform (either 'add' or 'remove')
	 * @param integer $quantity The number of coins to add or remove
	 * @return Response
	 * @throws Symfony\Component\HttpKernel\Exception\NotFoundHttpException
	 */
	public function addRemoveAction($id, $type, $quantity) {
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

			$mintage = $coin->getMintage();
			if ($mintage > 0 && $coin->getMemberTotal() + $quantity > $mintage) {
				throw $this->createNotFoundException($translator->trans('coin.not_available'));
			}

			if (!$uc) {
				$uc = new UserCoin();
				$uc->setUser($user);
				$uc->setCoin($coin);
				$uc->setQuantity($quantity);

				$em->persist($uc);
			} else {
				$uc->addUnit($quantity);
			}

			$coin->addUnit($quantity);
		} else if ($type === 'remove') {
			if (!$uc || $uc->getQuantity() === 0) {
				throw $this->createNotFoundException($translator->trans('user.coin_not_found'));
			}

			$coin = $em->getRepository('EuroCoinBundle:Coin')->find($id);

			if (!$coin) {
				throw $this->createNotFoundException($translator->trans('coin.not_found'));
			}

			if ($coin->getMemberTotal() >= $quantity && $uc->getQuantity() >= $quantity) {
				$coin->removeUnit($quantity);
				$uc->removeUnit($quantity);
			}
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
		$collator = new \Collator($this->getRequest()->getLocale());
		usort($countries, function ($a, $b) use ($collator, &$country, $id, $translator) {
					$a_name = $translator->trans((string) $a);
					$b_name = $translator->trans((string) $b);

					if ($id == $a->getId()) {
						$country = $a;
					} else if ($id == $b->getId()) {
						$country = $b;
					}

					return $collator->compare($a_name, $b_name);
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
			if (!isset($year_range[1]) && $year_range[0] > 0) {
				$year_range[1] = $year_range[0];
			}

			$base_coins = $coin_repo->findCoinsByCountry($country, $type, $year_range);
			$user = $this->getUser();

			if ($type == Coin::TYPE_CIRCULATION) {
				foreach ($base_coins as $coin) {
					$value = (string) $coin->getValue()->getValue();

					if (!$user) {
						if (!isset($totals[$value])) {
							$totals[$value] = $coin->getMintage();
						} else {
							$totals[$value] += $coin->getMintage();
						}
					}
				}

				$order = 'desc';
				if ($user) {
					$order = $user->getCoinsSort();
				}

				list($coins, $values) = $this->_buildVars($base_coins, $order);

				$coins = array_shift($coins);
				$values = array_shift($values);
			} else {
				$coins = $base_coins;
			}

			if ($user !== null) {
				$totals = array_fill_keys($values, 0);
				$user_coins = array();
				if (!empty($base_coins)) {
					$user_coins = $doctrine->getRepository('EuroCoinBundle:UserCoin')->findByCoinsForUser($user, $base_coins);
				}

				unset($base_coins);

				foreach ($user_coins as $user_coin) {
					$coin = $user_coin->getCoin();
					$quantity = $user_coin->getQuantity();
					$value = (string) $coin->getValue()->getValue();

					if (!isset($totals[$value])) {
						$totals[$value] = $quantity;
					} else {
						$totals[$value] += $quantity;
					}

					if ($quantity > 0) {
						$uc[$coin->getId()] = $user_coin;
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
					'year' => $year_range,
					'years' => $years,
				));
	}

	public function findAction(Request $request) {
		$params = array_filter(array(
			'country' => $request->get('countries'),
			'type' => $request->get('types'),
			'value' => $request->get('values'),
			'year' => $request->get('years'),
				));

		$matches = $this->getDoctrine()->getRepository('EuroCoinBundle:Coin')->findByParams($params);
		$translator = $this->get('translator');

		$results = array(
			'countries' => array(),
			'types' => array(),
			'values' => array(),
			'years' => array(),
		);
		foreach ($matches as $match) {
			$country = $match->getCountry();
			$type = $match->getType();
			$value = $match->getValue();
			$year = $match->getYear();

			$results['countries'][$country->getId()] = $translator->trans((string) $country);
			$results['types'][$type] = $translator->trans('coin.type' . $type);
			$results['values'][$value->getId()] = (string) $value;
			$results['years'][$year->getId()] = (string) $year;
		}

		asort($results['countries']);

		$response = new Response(json_encode($results));
		$response->headers->set('Content-Type', 'application/json');

		return $response;
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

	/**
	 * Compute various stats about the whole site
	 * @return Response
	 */
	public function statsAction() {
		$collator = new \Collator($this->getRequest()->getLocale());
		$doctrine = $this->getDoctrine();
		$translator = $this->get('translator');

		// User stats
		$user_repo = $doctrine->getRepository('ApplicationSonataUserBundle:User');
		$user_stats = array(
			'age' => $user_repo->findAgeStats(),
			'country' => array(),
			'gender' => array(),
			'total' => (int) $user_repo->count(),
		);

		foreach ($user_repo->findFromStats() as $country) {
			$user_stats['country'][$country['country']] = (int) $country['total'];
		}

		foreach ($user_repo->findGendersStats() as $gender) {
			$user_stats['gender'][$gender['gender']] = (int) $gender['total'];
		}

		// Sort the countries by translated name
		$countriesList = Locale::getDisplayCountries($this->getRequest()->getLocale());
		uksort($user_stats['country'], function ($a, $b) use ($collator, $countriesList, $translator, $user_stats) {
					$a_value = $user_stats['country'][$a];
					$b_value = $user_stats['country'][$b];

					if ($a_value != $b_value) {
						return $b_value - $a_value;
					}

					$a_name = $countriesList[$a];
					$b_name = $countriesList[$b];

					return $collator->compare($a_name, $b_name);
				});

		// Euro Zone stats
		$coin_repo = $doctrine->getRepository('EuroCoinBundle:Coin');
		$coins = $coin_repo->findCoinsStats();
		$countries = array();
		$euro_stats = array(
			'country' => array(),
			'total_coins' => 0,
			'total_countries' => 0,
		);

		foreach ($coins as $coin) {
			$country = $coin[0]->getCountry();

			$countries[strtoupper($country->getNameIso())] = $country;

			$euro_stats['country'][$country->getId()] = array(
				'mintage' => $coin['mintage'],
				'total' => $coin['total'],
			);
			$euro_stats['total_coins'] += $coin['total'];
		}

		$euro_stats['total_countries'] = count($countries);

		// Sort the countries by translated name
		uasort($countries, function ($a, $b) use ($collator, $translator) {
					$a_name = $translator->trans((string) $a);
					$b_name = $translator->trans((string) $b);

					return $collator->compare($a_name, $b_name);
				});

		// Collection stats
		$uc_repo = $doctrine->getRepository('EuroCoinBundle:UserCoin');

		return $this->render('EuroCoinBundle:Coin:stats.html.twig', array(
					'biggest_collections' => $uc_repo->findBiggestCollections(),
					'countries' => $countries,
					'country_stats' => $coin_repo->findTopCountries(),
					'euro_stats' => $euro_stats,
					'most_valued_collections' => $uc_repo->findMostValuedCollections(),
					'user_stats' => $user_stats,
				));
	}

}
