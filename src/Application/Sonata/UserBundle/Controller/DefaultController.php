<?php

namespace Application\Sonata\UserBundle\Controller;

use Euro\CoinBundle\Controller\BaseController;
use Euro\CoinBundle\Entity\Coin;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends BaseController {

	public function collectionAction($type, $country_id, $user_id) {
		$flashBag = $this->get('session')->getFlashBag();
		if (!$user = $this->getUser()) {
			$flashBag->add('error', 'user.login_required');

			return $this->redirect($this->generateUrl('fos_user_security_login'));
		}

		if ($user->getId() != $user_id) {
			$user = $this->getDoctrine()->getRepository('ApplicationSonataUserBundle:User')->find($user_id);

			if (!$user) {
				$flashBag->add('error', 'user.not_found');

				return $this->redirect($this->generateUrl('welcome'));
			}
		}

		$doctrine = $this->getDoctrine();
		$translator = $this->get('translator');

		$coins = array();
		$countries = array();
		$country = null;
		$uc = array();
		$user_coins = $doctrine->getRepository('EuroCoinBundle:UserCoin')->findCoinsByUser($user, $type);
		$values = array();

		if (!$user_coins) {
			if ($type == Coin::TYPE_CIRCULATION) {
				$type = Coin::TYPE_COMMEMORATIVE;
			} else if ($type == Coin::TYPE_COMMEMORATIVE) {
				$type = Coin::TYPE_COLLECTOR;
			} else {
				$flashBag->add('info', 'user.has_no_such_coin');

				return $this->redirect($this->generateUrl('show_profile', array(
									'user_id' => $user_id,
								)));
			}

			return $this->redirect($this->generateUrl('show_user_collection' . $type, array(
								'user_id' => $user_id,
							)));
		}

		foreach ($user_coins as $user_coin) {
			$coin = $user_coin->getCoin();

			if ($user_coin->getQuantity() > 0) {
				$_country = $coin->getCountry();
				$ct_id = $_country->getId();
				if (!isset($countries[$ct_id])) {
					$countries[$ct_id] = $_country;
				}

				if ($ct_id == $country_id) {
					$country = $_country;
				}

				$uc[$coin->getId()] = $user_coin;
			}
		}

		if ($countries) {
			// Sort the countries by translated name
			$collator = new \Collator($this->getRequest()->getLocale());
			usort($countries, function ($a, $b) use ($collator, &$country, $country_id, $translator) {
						$a_name = $translator->trans((string) $a);
						$b_name = $translator->trans((string) $b);

						if ($country_id == $a->getId()) {
							$country = $a;
						} else if ($country_id == $b->getId()) {
							$country = $b;
						}

						return $collator->compare($a_name, $b_name);
					});

			if ($type != Coin::TYPE_CIRCULATION) {
				$coins = array();
				foreach ($user_coins as $user_coin) {
					$coins[] = $user_coin->getCoin();
				}

				if (!$country) {
					$country = reset($countries);
				}
			} else {
				list($coins, $values) = $this->_buildVars($user_coins);

				$country_name = $translator->trans((string) $country);

				if (!isset($coins[$country_name])) {
					$country = reset($countries);
					$country_name = $translator->trans((string) $country);
				}

				$coins = $coins[$country_name];
				$values = $values[$country_name];
			}

			if ($country && (!$country_id || $country_id != $country->getId())) {
				return $this->redirect($this->generateUrl($this->getRequest()->get('_route'), array(
									'country_id' => $country->getId(),
									'country_name' => $translator->trans((string) $country),
									'user_id' => $user_id,
								)));
			}
		}

		$template_file = 'collection';
		if ($type != Coin::TYPE_CIRCULATION) {
			$template_file = 'collection_collector';
		}

		return $this->render('ApplicationSonataUserBundle:Profile:' . $template_file . '.html.twig', array(
					'all_values' => $values,
					'coins' => $coins,
					'type' => $type,
					'countries' => $countries,
					'current' => $country,
					'uc' => $uc,
					'user' => $user,
				));
	}

	public function queryAction() {
		$translator = $this->get('translator');

		if (!$user = $this->getUser()) {
			throw $this->createNotFoundException($translator->trans('user.login_required'));
		}

		$query = strtolower($this->getRequest()->request->get('query'));

		$queryBuilder = $this->getDoctrine()->getEntityManager()->createQueryBuilder();
		$expr = $queryBuilder->expr();

		$users = $queryBuilder
				->select('u')
				->from('Application\Sonata\UserBundle\Entity\User', 'u')
				->where($expr->like($expr->lower('u.username'), $expr->literal('%' . $query . '%')))
				->andWhere($expr->neq('u.id', ':id'))
				->orderBy('u.username')
				->setParameter('id', $user->getId())
				->getQuery()
				->getResult();

		$result = array();
		foreach ($users as $user) {
			$result[$user->getId()] = $user->getUsername();
		}

		$response = new Response(json_encode($result));
		$response->headers->set('Content-Type', 'application/json');

		return $response;
	}

	public function showAction($id) {
		if (!$user = $this->getUser()) {
			$this->get('session')->getFlashBag()->add('error', 'user.login_required');

			return $this->redirect($this->generateUrl('fos_user_security_login'));
		}

		if ($user->getId() != $id) {
			$user = $this->getDoctrine()->getRepository('ApplicationSonataUserBundle:User')->find($id);

			if (!$user) {
				$this->get('session')->getFlashBag()->add('error', 'user.not_found');

				return $this->redirect($this->generateUrl('welcome'));
			}
		}

		return $this->render('ApplicationSonataUserBundle:Profile:show.html.twig', array(
					'user' => $user,
				));
	}

	public function statsAction($id) {
		if (!$user = $this->getUser()) {
			$this->get('session')->getFlashBag()->add('error', 'user.login_required');

			return $this->redirect($this->generateUrl('fos_user_security_login'));
		}

		$doctrine = $this->getDoctrine();
		if ($user->getId() != $id) {
			$user = $doctrine->getRepository('ApplicationSonataUserBundle:User')->find($id);

			if (!$user) {
				$this->get('session')->getFlashBag()->add('error', 'user.not_found');

				return $this->redirect($this->generateUrl('welcome'));
			}
		}

		$translator = $this->get('translator');
		$user_coins = $doctrine->getRepository('EuroCoinBundle:UserCoin')->findByUser($user);
		$countries = array();
		$countries_data = array();
		$countries_stats = array();
		$countries_user_stats = array();
		$global = array(
			'average_value' => 0,
			'countries' => array(),
			'total_coins' => count($user_coins),
			'total_collectors' => 0,
			'total_commemoratives' => 0,
			'total_doubles' => 0,
			'total_uniques' => 0,
			'total_uniques_value' => 0,
			'total_value' => 0,
		);
		$years = array();

		if ($user_coins) {
			foreach ($user_coins as $uc) {
				$coin = $uc->getCoin();
				$country = $coin->getCountry();
				$country_id = $country->getId();
				$quantity = $uc->getQuantity();
				$value = $coin->getValue();
				$year = $coin->getYear();
				$year_id = $year->getId();

				// Global stats
				if (!isset($global['countries'][$country_id])) {
					$global['countries'][$country_id] = $country;
				}

				switch ($coin->getType()) {
					case Coin::TYPE_COLLECTOR :
						++$global['total_collectors'];
						break;

					case Coin::TYPE_COMMEMORATIVE :
						++$global['total_commemoratives'];
						break;
				}

				if ($quantity === 1) {
					++$global['total_uniques'];
				} else if ($quantity > 1) {
					++$global['total_doubles'];
				}

				$global['total_uniques_value'] += $value->getValue();
				$global['total_value'] += $value->getValue() * $quantity;

				// Retrieve country list
				if (!isset($countries[$country_id])) {
					$countries[$country_id] = $country;
				}

				// Per country stat
				if (!isset($countries_user_stats[$country_id])) {
					$countries_user_stats[$country_id] = array(
						'total_coins' => 0,
						'total_collectors' => 0,
						'total_commemoratives' => 0,
						'total_value' => 0,
						'years' => array(),
					);
				}

				$stat = &$countries_user_stats[$country_id];

				++$stat['total_coins'];

				switch ($coin->getType()) {
					case Coin::TYPE_COLLECTOR :
						++$stat['total_collectors'];
						break;

					case Coin::TYPE_COMMEMORATIVE :
						++$stat['total_commemoratives'];
						break;
				}

				$stat['total_value'] += $value->getValue();

				if (!isset($stat['years'][$year_id])) {
					$stat['years'][$year_id] = 0;
				}
				++$stat['years'][$year_id];
			}

			if ($global['total_coins'] > 0) {
				$global['average_value'] = $global['total_value'] / $global['total_coins'];
			}

			$countries_data = $doctrine->getRepository('EuroCoinBundle:Coin')->findByCountry($countries);

			foreach ($countries_data as $coin) {
				$country = $coin->getCountry();
				$country_id = $country->getId();
				$value = $coin->getValue();
				$year = $coin->getYear();
				$year_id = $year->getId();

				if (!isset($countries_stats[$country_id])) {
					$countries_stats[$country_id] = array(
						'total_coins' => 0,
						'total_collectors' => 0,
						'total_commemoratives' => 0,
						'total_value' => 0,
						'years' => array(),
					);
				}

				$stat = &$countries_stats[$country_id];

				++$stat['total_coins'];

				switch ($coin->getType()) {
					case Coin::TYPE_COLLECTOR :
						++$stat['total_collectors'];
						break;

					case Coin::TYPE_COMMEMORATIVE :
						++$stat['total_commemoratives'];
						break;
				}

				$stat['total_value'] += $value->getValue();

				if (!isset($stat['years'][$year_id])) {
					$stat['years'][$year_id] = 0;
					$years[$year_id] = $year;
				}
				++$stat['years'][$year_id];
			}

			// Sort the countries by translated name
			$collator = new \Collator($this->getRequest()->getLocale());
			usort($global['countries'], function ($a, $b) use ($collator, $translator) {
						$a_name = $translator->trans((string) $a);
						$b_name = $translator->trans((string) $b);

						return $collator->compare($a_name, $b_name);
					});
		}

		return $this->render('ApplicationSonataUserBundle:Profile:stats.html.twig', array(
					'countries' => $countries,
					'countries_stats' => $countries_stats,
					'countries_user_stats' => $countries_user_stats,
					'global' => $global,
					'user' => $user,
					'years' => $years,
				));
	}

}
