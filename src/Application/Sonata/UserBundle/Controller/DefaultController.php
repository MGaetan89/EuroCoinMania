<?php

namespace Application\Sonata\UserBundle\Controller;

use Euro\CoinBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;

class DefaultController extends BaseController {

	public function collectionAction($collector, $country_id, $user_id) {
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

		$country = null;
		$uc = array();
		$user_coins = $doctrine->getRepository('EuroCoinBundle:UserCoin')->findCoinsByUser($user, $collector);
		$values = array();

		if ($collector) {
			$coins = array();
			foreach ($user_coins as $user_coin) {
				$coins[] = $user_coin->getCoin();
			}
		} else {
			list($coins, $values) = $this->_buildVars($user_coins);

			$coins = array_shift($coins);
			$values = array_shift($values);
		}

		$countries = array();
		foreach ($user_coins as $user_coin) {
			$coin = $user_coin->getCoin();

			if ($user_coin->getQuantity() > 0) {
				$country = $coin->getCountry();
				$ct_id = $country->getId();
				if (!isset($countries[$ct_id])) {
					$countries[$ct_id] = $country;
				}

				$uc[$coin->getId()] = $user_coin;
			}
		}

		if (!$country_id && $country) {
			return $this->redirect($this->generateUrl('show_user_collection', array(
								'country_id' => $country->getId(),
								'country_name' => $translator->trans((string) $country),
								'user_id' => $user_id,
							)));
		}

		// Sort the countries by translated name
		usort($countries, function ($a, $b) use (&$country, $country_id, $translator) {
					$a_name = $translator->trans((string) $a->getName());
					$b_name = $translator->trans((string) $b->getName());

					if ($country_id == $a->getId()) {
						$country = $a;
					}

					return strcmp($a_name, $b_name);
				});

		$template_file = 'collection';
		if ($collector) {
			$template_file = 'collection_collector';
		}

		return $this->render('ApplicationSonataUserBundle:Profile:' . $template_file . '.html.twig', array(
					'all_values' => $values,
					'coins' => $coins,
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

}
