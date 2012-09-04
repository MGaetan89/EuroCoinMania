<?php

namespace Euro\CoinBundle\Controller;

use Euro\CoinBundle\Entity\Exchange;
use Euro\PrivateMessageBundle\Entity\Conversation;
use Euro\PrivateMessageBundle\Entity\Message;

class ExchangeController extends BaseController {

	public function acceptAction($id) {
		// @Todo : implement and decide action to perform with 'UserCoin.sharing'
	}

	public function cancelRefuseAction($id, $refuse) {
		$flashBag = $this->get('session')->getFlashBag();
		if (!$user = $this->getUser()) {
			$flashBag->add('error', 'user.login_required');

			return $this->redirect($this->generateUrl('exchange_list'));
		}

		$doctrine = $this->getDoctrine();
		$exchange = $doctrine->getRepository('EuroCoinBundle:Exchange')->find($id);

		if (!$exchange) {
			$flashBag->add('error', 'exchange.not_found');

			return $this->redirect($this->generateUrl('exchange_list'));
		}

		if ($refuse) {
			$condition = $exchange->getToUser()->getId() !== $user->getId();
			$error = 'exchange.not_intended';
			$pm_text = 'exchange_refused';
			$pm_type = Message::TYPE_DANGER;
		} else {
			$condition = $exchange->getFromUser()->getId() !== $user->getId();
			$error = 'exchange.not_own';
			$pm_text = 'exchange_canceled';
			$pm_type = Message::TYPE_WARNING;
		}

		if ($condition) {
			$flashBag->add('error', $error);

			return $this->redirect($this->generateUrl('exchange_list'));
		}

		$exchange->setStatus($refuse ? Exchange::STATUS_REFUSED : Exchange::STATUS_CANCELED);

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

		if ($refuse) {
			$flashBag->add('success', 'exchange.refused');
		} else {
			$flashBag->add('success', 'exchange.canceled');
		}

		return $this->redirect($this->generateUrl('exchange_list'));
	}

	public function chooseCoinsAction($id) {
		$flashBag = $this->get('session')->getFlashBag();
		if (!$user = $this->getUser()) {
			$flashBag->add('error', 'user.login_required');

			return $this->redirect($this->generateUrl('coin_collection'));
		}

		$doctrine = $this->getDoctrine();
		$from = $doctrine->getRepository('ApplicationSonataUserBundle:User')->find($id);

		if (!$from || $user === $from) {
			if (!$from) {
				$flashBag->add('error', 'coin.doubles.user_not_found');
			} else {
				$flashBag->add('error', 'coin.doubles.no_self_exchange');
			}

			return $this->redirect($this->generateUrl('exchange_choose_user'));
		}

		$user_coins = $doctrine->getRepository('EuroCoinBundle:UserCoin')->findDoublesFromUser($from);

		if (!$user_coins) {
			$flashBag->add('error', 'coin.doubles.user_no_doubles');

			return $this->redirect($this->generateUrl('exchange_choose_user'));
		}

		list($coins, $values) = $this->_buildVars($user_coins);

		return $this->render('EuroCoinBundle:Exchange:coins_request.html.twig', array(
					'all_values' => $values,
					'coins' => $coins,
					'from' => $from,
				));
	}

	public function chooseUserAction() {
		if (!$user = $this->getUser()) {
			$this->get('session')->getFlashBag()->add('error', 'user.login_required');

			return $this->redirect($this->generateUrl('coin_collection'));
		}

		$users = $this->getDoctrine()->getRepository('EuroCoinBundle:UserCoin')->findDoublesForUser($user);

		if (!$users) {
			$this->get('session')->getFlashBag()->add('info', 'coin.doubles.none');

			return $this->redirect($this->generateUrl('exchange_list'));
		}

		return $this->render('EuroCoinBundle:Exchange:choose_user.html.twig', array(
					'users' => $users,
				));
	}

	public function listAction($all) {
		$flashBag = $this->get('session')->getFlashBag();
		if (!$user = $this->getUser()) {
			$flashBag->add('error', 'user.login_required');

			return $this->redirect($this->generateUrl('coin_collection'));
		}

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
				$flashBag->add('info', 'exchange.none_pending');

				return $this->redirect($this->generateUrl('exchange_list_all'));
			} else {
				$flashBag->clear();
				$flashBag->add('info', 'exchange.none');

				return $this->redirect($this->generateUrl('exchange_choose_user'));
			}

			$flashBag->clear();
			$flashBag->add('info', 'exchange.none');

			return $this->redirect($this->generateUrl('coin_collection'));
		}

		return $this->render('EuroCoinBundle:Exchange:list.html.twig', array(
					'all' => $all,
					'coins' => $coins,
					'exchanges' => $exchanges,
				));
	}

	public function proposeCoinsAction($id) {
		$translator = $this->get('translator');

		if (!$user = $this->getUser()) {
			throw $this->createNotFoundException($translator->trans('user.login_required'));
		}

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

		$from_coins = $doctrine->getRepository('EuroCoinBundle:UserCoin')->findBy(array(
			'coin' => $coins_id,
			'user' => $from,
				));

		if (count($coins_id) !== count($from_coins)) {
			throw $this->createNotFoundException($translator->trans('coin.doubles.some_not_found'));
		}

		$user_coins = $doctrine->getRepository('EuroCoinBundle:UserCoin')->findDoublesFromUserAndCoins($user, $coins_id);

		if (!$user_coins) {
			throw $this->createNotFoundException($translator->trans('coin.doubles.user_no_doubles'));
		}

		list($coins, $values) = $this->_buildVars($user_coins);

		$total_requested = 0;
		foreach ($from_coins as $uc) {
			$total_requested += $uc->getCoin()->getValue()->getValue();
		}

		$this->getRequest()->getSession()->set('from_coins', $coins_id);

		return $this->render('EuroCoinBundle:Exchange:coins_suggest.html.twig', array(
					'all_values' => $values,
					'from' => $from,
					'from_coins' => $from_coins,
					'coins' => $coins,
					'total_requested' => $total_requested,
				));
	}

	public function saveAction($id) {
		$translator = $this->get('translator');

		if (!$user = $this->getUser()) {
			throw $this->createNotFoundException($translator->trans('user.login_required'));
		}

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

		$this->get('session')->getFlashBag()->add('success', 'coin.doubles.save_successfull');

		return $this->redirect($this->generateUrl('exchange_show', array(
							'id' => $exchange->getId(),
						)));
	}

	public function showAction($id) {
		if (!$user = $this->getUser()) {
			$this->get('session')->getFlashBag()->add('error', 'user.login_required');

			return $this->redirect($this->generateUrl('coin_collection'));
		}

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
