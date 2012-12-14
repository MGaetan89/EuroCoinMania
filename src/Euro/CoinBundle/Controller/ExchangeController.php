<?php

namespace Euro\CoinBundle\Controller;

use Euro\CoinBundle\Entity\Coin;
use Euro\CoinBundle\Entity\Exchange;
use Euro\PrivateMessageBundle\Entity\Conversation;
use Euro\PrivateMessageBundle\Entity\Message;

class ExchangeController extends BaseController {

	public function acceptCancelRefuseAction($id, $type) {
		$flashBag = $this->get('session')->getFlashBag();
		$user = $this->getUser();
		$doctrine = $this->getDoctrine();
		$exchange = $doctrine->getRepository('EuroCoinBundle:Exchange')->find($id);

		if (!$exchange) {
			$flashBag->add('error', 'exchange.not_found');

			return $this->redirect($this->generateUrl('exchange_list'));
		}

		switch ($type) {
			case 'accept' :
				$condition = $exchange->getToUser()->getId() !== $user->getId();
				$error = 'exchange.not_intended';
				$flash_message = 'exchange.accepted';
				$message_type = Exchange::STATUS_ACCEPTED;
				$pm_text = 'exchange_accepted';
				$pm_type = Message::TYPE_SUCCESS;

				break;

			case 'cancel' :
				$condition = $exchange->getFromUser()->getId() !== $user->getId();
				$error = 'exchange.not_own';
				$flash_message = 'exchange.canceled';
				$message_type = Exchange::STATUS_CANCELED;
				$pm_text = 'exchange_canceled';
				$pm_type = Message::TYPE_WARNING;

				break;

			case 'refuse' :
				$condition = $exchange->getToUser()->getId() !== $user->getId();
				$error = 'exchange.not_intended';
				$flash_message = 'exchange.refused';
				$message_type = Exchange::STATUS_REFUSED;
				$pm_text = 'exchange_refused';
				$pm_type = Message::TYPE_DANGER;

				break;
		}

		if ($condition) {
			$flashBag->add('error', $error);

			return $this->redirect($this->generateUrl('exchange_list'));
		}

		$exchange->setStatus($message_type);

		$user_coin = $doctrine->getRepository('EuroCoinBundle:UserCoin');
		$coins = $user_coin->findBy(array(
			'coin' => $exchange->getCoinsSuggested(),
			'user' => $exchange->getFromUser(),
				));

		foreach ($coins as $uc) {
			$uc->removeExchange();
		}

		$coins = $user_coin->findBy(array(
			'coin' => $exchange->getCoinsRequested(),
			'user' => $exchange->getToUser(),
				));

		foreach ($coins as $uc) {
			$uc->removeExchange();
		}

		$conversation = $exchange->getConversation();

		$message = new Message();
		$message->setContent('pm.text.' . $pm_text);
		$message->setType($pm_type);
		$message->setConversation($conversation);

		if ($exchange->getFromUser()->getId() === $user->getId()) {
			$message->setDirection(Message::DIRECTION_FROM_TO);
		} else {
			$message->setDirection(Message::DIRECTION_TO_FROM);
		}

		$em = $doctrine->getManager();
		$em->persist($message);

		$conversation->addMessage($message);

		$em->flush();

		$flashBag->add('success', $flash_message);

		return $this->redirect($this->generateUrl('exchange_show', array(
							'id' => $exchange->getId(),
						)));
	}

	public function chooseCoinsAction($id) {
		$flashBag = $this->get('session')->getFlashBag();
		$user = $this->getUser();
		$doctrine = $this->getDoctrine();
		$from = $doctrine->getRepository('ApplicationSonataUserBundle:User')->find($id);
		$uc_repo = $doctrine->getRepository('EuroCoinBundle:UserCoin');

		if (!$from || $user === $from) {
			if (!$from) {
				$flashBag->add('error', 'coin.doubles.user_not_found');
			} else {
				$flashBag->add('error', 'coin.doubles.no_self_exchange');
			}

			return $this->redirect($this->generateUrl('exchange_choose_user'));
		}

		$coin_types = array(
			'circulation' => Coin::TYPE_CIRCULATION,
			'commemorative' => Coin::TYPE_COMMEMORATIVE,
			'collector' => Coin::TYPE_COLLECTOR,
		);
		$filter_types = array();

		foreach ($coin_types as $name => $type) {
			${'uc_' . $name} = $uc_repo->findDoublesFromUser($from, $type);

			if (count(${'uc_' . $name}) > 0) {
				$filter_types[] = $type;
			}
		}

		if (empty($filter_types)) {
			$flashBag->add('error', 'coin.doubles.user_no_doubles');

			return $this->redirect($this->generateUrl('exchange_choose_user'));
		}

		$filter_countries = array();
		$filter_values = array();
		$filter_years = array();
		$values = array();
		foreach (array_keys($coin_types) as $name) {
			list(${'coins_' . $name}, ${'values_' . $name}, ${'years_' . $name}, ${'countries_' . $name}) = $this->_buildVars(${'uc_' . $name}, $user->getCoinsSort());

			foreach (${'countries_' . $name} as $country) {
				$country_id = $country->getId();
				if (!isset($filter_countries[$country_id])) {
					$filter_countries[$country_id] = $country;
				}
			}

			foreach (${'values_' . $name} as $value) {
				$filter_values = array_merge($filter_values, $value);
			}

			foreach (${'years_' . $name} as $year) {
				$year_id = $year->getId();
				if (!isset($filter_years[$year_id])) {
					$filter_years[$year_id] = $year;
				}
			}
		}

		return $this->render('EuroCoinBundle:Exchange:choose_coins.html.twig', array(
					'coins' => array(
						'circulation' => $coins_circulation,
						'commemorative' => $coins_commemorative,
						'collector' => $coins_collector,
					),
					'filters' => array(
						'countries' => $filter_countries,
						'types' => $filter_types,
						'values' => array_unique($filter_values),
						'years' => $filter_years,
					),
					'from' => $from,
					'type' => 'request',
					'values' => array(
						'circulation' => $values_circulation,
						'commemorative' => $values_commemorative,
						'collector' => $values_collector,
					),
				));
	}

	public function chooseUserAction() {
		$user = $this->getUser();

		if (!$user->getAllowExchanges()) {
			$flashBag = $this->get('session')->getFlashBag();
			$flashBag->clear();
			$flashBag->add('info', 'user.exchanges.disabled');

			return $this->redirect($this->generateUrl('exchange_list'));
		}

		$users = $this->getDoctrine()->getRepository('EuroCoinBundle:UserCoin')->findDoublesForUser($user);

		if (!$users) {
			$flashBag = $this->get('session')->getFlashBag();
			$flashBag->clear();
			$flashBag->add('info', 'coin.doubles.none');

			return $this->redirect($this->generateUrl('exchange_list_all'));
		}

		return $this->render('EuroCoinBundle:Exchange:choose_user.html.twig', array(
					'users' => $users,
				));
	}

	public function listAction($all) {
		$flashBag = $this->get('session')->getFlashBag();
		$user = $this->getUser();
		$doctrine = $this->getDoctrine();
		$exchanges = $doctrine->getRepository('EuroCoinBundle:Exchange')->findForUser($user, $all);

		$coin_repo = $doctrine->getRepository('EuroCoinBundle:Coin');
		$coins = array();
		foreach ($exchanges as $exchange) {
			$coins[$exchange->getId()] = array(
				'requested' => $coin_repo->findCoinById($exchange->getCoinsRequested()),
				'suggested' => $coin_repo->findCoinById($exchange->getCoinsSuggested()),
			);
		}

		if (!$exchanges) {
			if (!$all) {
				$flashBag->clear();
				$flashBag->add('info', 'exchange.none_pending');

				return $this->redirect($this->generateUrl('exchange_list_all'));
			}
		}

		return $this->render('EuroCoinBundle:Exchange:list.html.twig', array(
					'all' => $all,
					'coins' => $coins,
					'exchanges' => $exchanges,
				));
	}

	public function proposeCoinsAction($id) {
		$flashBag = $this->get('session')->getFlashBag();
		$translator = $this->get('translator');
		$user = $this->getUser();
		$doctrine = $this->getDoctrine();
		$from = $doctrine->getRepository('ApplicationSonataUserBundle:User')->find($id);
		$uc_repo = $doctrine->getRepository('EuroCoinBundle:UserCoin');

		if (!$from) {
			throw $this->createNotFoundException($translator->trans('coin.doubles.user_not_found'));
		}

		if ($user === $from) {
			throw $this->createNotFoundException($translator->trans('coin.doubles.no_self_exchange'));
		}

		$coins_id = explode(',', $this->getRequest()->request->get('coins'));
		$coins_id = array_map('intval', $coins_id);

		if (!$coins_id) {
			throw $this->createNotFoundException($translator->trans('coin.doubles.none_selected'));
		}

		$from_coins = $doctrine->getRepository('EuroCoinBundle:UserCoin')->findBy(array(
			'coin' => $coins_id,
			'user' => $from,
				));

		if (count($coins_id) !== count($from_coins)) {
			throw $this->createNotFoundException($translator->trans('coin.doubles.some_not_found'));
		}

		$coin_types = array(
			'circulation' => Coin::TYPE_CIRCULATION,
			'commemorative' => Coin::TYPE_COMMEMORATIVE,
			'collector' => Coin::TYPE_COLLECTOR,
		);
		$filter_types = array();

		foreach ($coin_types as $name => $type) {
			${'uc_' . $name} = $uc_repo->findDoublesFromUserAndCoins($user, $coins_id, $type);

			if (count(${'uc_' . $name}) > 0) {
				$filter_types[] = $type;
			}
		}

		if (empty($filter_types)) {
			$flashBag->add('error', 'coin.doubles.user_no_doubles');

			return $this->redirect($this->generateUrl('exchange_choose_user'));
		}

		$filter_countries = array();
		$filter_values = array();
		$filter_years = array();
		$values = array();
		foreach (array_keys($coin_types) as $name) {
			list(${'coins_' . $name}, ${'values_' . $name}, ${'years_' . $name}, ${'countries_' . $name}) = $this->_buildVars(${'uc_' . $name}, $user->getCoinsSort());

			foreach (${'countries_' . $name} as $country) {
				$country_id = $country->getId();
				if (!isset($filter_countries[$country_id])) {
					$filter_countries[$country_id] = $country;
				}
			}

			foreach (${'values_' . $name} as $value) {
				$filter_values = array_merge($filter_values, $value);
			}

			foreach (${'years_' . $name} as $year) {
				$year_id = $year->getId();
				if (!isset($filter_years[$year_id])) {
					$filter_years[$year_id] = $year;
				}
			}
		}

		$total_requested = 0;
		foreach ($from_coins as $uc) {
			$total_requested += $uc->getCoin()->getValue()->getValue();
		}

		$this->getRequest()->getSession()->set('from_coins', $coins_id);

		return $this->render('EuroCoinBundle:Exchange:choose_coins.html.twig', array(
					'coins' => array(
						'circulation' => $coins_circulation,
						'commemorative' => $coins_commemorative,
						'collector' => $coins_collector,
					),
					'filters' => array(
						'countries' => $filter_countries,
						'types' => $filter_types,
						'values' => array_unique($filter_values),
						'years' => $filter_years,
					),
					'from' => $from,
					'from_coins' => $from_coins,
					'total_requested' => $total_requested,
					'type' => 'suggest',
					'values' => array(
						'circulation' => $values_circulation,
						'commemorative' => $values_commemorative,
						'collector' => $values_collector,
					),
				));
	}

	public function saveAction($id) {
		$translator = $this->get('translator');
		$user = $this->getUser();
		$doctrine = $this->getDoctrine();
		$from = $doctrine->getRepository('ApplicationSonataUserBundle:User')->find($id);

		if (!$from) {
			throw $this->createNotFoundException($translator->trans('coin.doubles.user_not_found'));
		}

		if ($user === $from) {
			throw $this->createNotFoundException($translator->trans('coin.doubles.no_self_exchange'));
		}

		$coins_id = explode(',', $this->getRequest()->request->get('coins'));
		$coins_id = array_map('intval', $coins_id);

		if (!$coins_id) {
			throw $this->createNotFoundException($translator->trans('coin.doubles.none_selected'));
		}

		$to_coins = $doctrine->getRepository('EuroCoinBundle:UserCoin')->findBy(array(
			'coin' => $coins_id,
			'user' => $user,
				));

		if (count($coins_id) !== count($to_coins)) {
			throw $this->createNotFoundException($translator->trans('coin.doubles.some_not_found'));
		}

		$from_coins_id = $this->getRequest()->getSession()->get('from_coins');

		$exchange = new Exchange();
		$exchange->setStatus(Exchange::STATUS_PENDING);
		$exchange->setFromUser($user);
		$exchange->setToUser($from);
		$exchange->setCoinsRequested($from_coins_id);
		$exchange->setCoinsSuggested($coins_id);

		$conversation = new Conversation();
		$conversation->setFromUser($user);
		$conversation->setToUser($from);
		$conversation->setTitle('pm.title.new_exchange');
		$conversation->setExchange($exchange);

		$message = new Message();
		$message->setContent('pm.text.new_exchange');
		$message->setType(Message::TYPE_INFO);
		$message->setConversation($conversation);

		$em = $doctrine->getManager();
		$em->persist($conversation);
		$em->persist($message);
		$em->persist($exchange);

		$conversation->addMessage($message);
		$exchange->setConversation($conversation);

		$from_coins = $doctrine->getRepository('EuroCoinBundle:UserCoin')->findBy(array(
			'coin' => $from_coins_id,
			'user' => $from,
				));

		foreach ($from_coins as $uc) {
			$uc->addExchange();
		}

		foreach ($to_coins as $uc) {
			$uc->addExchange();
		}

		$em->flush();

		if ($from->getExchangeNotification()) {
			$emailLocale = $from->getLocale();
			$message = \Swift_Message::newInstance()
				->setSubject($translator->trans('exchange.email.title.new_exchange', array(), 'messages', $emailLocale))
				->setFrom(array('contact@eurocoin-mania.eu' => 'EuroCoin Mania'))
				->setTo($from->getEmail())
				->setBody($translator->trans('exchange.email.text.new_exchange', array(
					'%path%' => $this->generateUrl('exchange_show', array(
						'id' => $exchange->getId(),
					), true),
					'%username%' => $user->getUsername(),
				), 'messages', $emailLocale));

			$this->get('mailer')->send($message);
		}

		$this->get('session')->getFlashBag()->add('success', 'coin.doubles.save_successfull');

		return $this->redirect($this->generateUrl('exchange_show', array(
					'id' => $exchange->getId(),
				)));
	}

	public function searchAction() {
		$doctrine = $this->getDoctrine();
		$translator = $this->get('translator');
		$countries = $doctrine->getRepository('EuroCoinBundle:Country')->findAll();
		$values = $doctrine->getRepository('EuroCoinBundle:Value')->findBy(array(), array(
			'value' => 'ASC',
		));
		$years = $doctrine->getRepository('EuroCoinBundle:Year')->findAllSorted();

		$collator = new \Collator($this->getRequest()->getLocale());
		usort($countries, function ($a, $b) use ($collator, $translator) {
					return $collator->compare($translator->trans((string) $a), $translator->trans((string) $b));
				});

		return $this->render('EuroCoinBundle:Exchange:search.html.twig', array(
			'countries' => $countries,
			'types' => array(
				Coin::TYPE_CIRCULATION, Coin::TYPE_COMMEMORATIVE, Coin::TYPE_COLLECTOR,
			),
			'values' => $values,
			'years' => $years,
		));
	}

	public function showAction($id) {
		$doctrine = $this->getDoctrine();
		$exchange = $doctrine->getRepository('EuroCoinBundle:Exchange')->find($id);

		if (!$exchange) {
			$this->get('session')->getFlashBag()->add('info', 'exchange.not_found');

			return $this->redirect($this->generateUrl('exchange_list'));
		}

		$coin_repo = $doctrine->getRepository('EuroCoinBundle:Coin');
		$coins = array(
			'requested' => $coin_repo->findCoinById($exchange->getCoinsRequested()),
			'suggested' => $coin_repo->findCoinById($exchange->getCoinsSuggested()),
		);

		return $this->render('EuroCoinBundle:Exchange:show.html.twig', array(
					'coins' => $coins,
					'counts' => array(
						'requested' => count($coins['requested']),
						'suggested' => count($coins['suggested']),
					),
					'exchange' => $exchange,
				));
	}

}
