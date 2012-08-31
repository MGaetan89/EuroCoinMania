<?php

namespace Application\Sonata\UserBundle\Controller;

use Euro\CoinBundle\Controller\BaseController;

class DefaultController extends BaseController {

	public function collectionAction($country_id, $user_id) {
		if (!$user = $this->getUser()) {
			$this->get('session')->getFlashBag()->add('error', 'user.login_required');

			return $this->redirect($this->generateUrl('fos_user_security_login'));
		}

		if ($user->getId() != $user_id) {
			$user = $this->getDoctrine()->getRepository('ApplicationSonataUserBundle:User')->find($user_id);

			if (!$user) {
				$this->get('session')->getFlashBag()->add('error', 'user.not_found');

				return $this->redirect($this->generateUrl('welcome'));
			}
		}

		$doctrine = $this->getDoctrine();
		$translator = $this->get('translator');
		$countries = $doctrine->getRepository('EuroCoinBundle:Country')->findBy(array(), array('join_date' => 'ASC'));
		$country = null;

		// Sort the countries by translated name
		usort($countries, function ($a, $b) use (&$country, $country_id, $translator) {
					$a_name = $translator->trans((string) $a->getName());
					$b_name = $translator->trans((string) $b->getName());

					if ($country_id == $a->getId()) {
						$country = $a;
					}

					return strcmp($a_name, $b_name);
				});

		if (!$country && $countries) {
			$country = $countries[0];

			if (!$country_id) {
				return $this->redirect($this->generateUrl('show_user_collection', array(
									'country_id' => $country->getId(),
									'country_name' => $translator->trans((string) $country),
									'user_id' => $user_id,
								)));
			}
		}

		$coins = array();
		$values = array();
		$uc = array();

		if ($country) {
			$user_coins = $doctrine->getRepository('EuroCoinBundle:UserCoin')->findCoinsByUserAndCountry($user, $country);

			list($coins, $values) = $this->_buildVars($user_coins);

			$coins = array_shift($coins);
			$values = array_shift($values);

			$countries = array();
			foreach ($user_coins as $user_coin) {
				$coin = $user_coin->getCoin();

				if ($user_coin->getQuantity() > 0) {
					$country = $coin->getCountry();
					$country_id = $country->getId();
					if (!isset($countries[$country_id])) {
						$countries[$country_id] = $country;
					}

					$uc[$coin->getId()] = $user_coin;
				}
			}
		}

		return $this->render('ApplicationSonataUserBundle:Profile:collection.html.twig', array(
					'all_values' => $values,
					'coins' => $coins,
					'countries' => $countries,
					'current' => $country,
					'uc' => $uc,
					'user' => $user,
				));
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

}
