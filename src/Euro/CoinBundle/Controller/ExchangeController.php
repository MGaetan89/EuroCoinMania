<?php

namespace Euro\CoinBundle\Controller;

use Euro\CoinBundle\Entity\Share;

class ExchangeController extends BaseController {

	public function acceptAction($id) {
		// @Todo : implement and decide actio to perform with 'UserCoin.sharing'
	}

	public function cancelRefuseAction($id, $refuse) {
		$flashBag = $this->get('session')->getFlashBag();
		if (!$user = $this->getUser()) {
			$flashBag->add('error', 'user.login_required');

			return $this->redirect($this->generateUrl('exchange_list'));
		}

		$doctrine = $this->getDoctrine();
		$share = $doctrine->getRepository('EuroCoinBundle:Share')->find($id);

		if (!$share) {
			$flashBag->add('error', 'exchange.not_found');

			return $this->redirect($this->generateUrl('exchange_list'));
		}

		if ($refuse) {
			$condition = $share->getToUser()->getId() !== $user->getId();
			$error = 'exchange.not_intended';
		} else {
			$condition = $share->getFromUser()->getId() !== $user->getId();
			$error = 'exchange.not_own';
		}

		if ($condition) {
			$flashBag->add('error', $error);

			return $this->redirect($this->generateUrl('exchange_list'));
		}

		$share->setStatus($refuse ? Share::STATUS_REFUSED : Share::STATUS_CANCELED);

		$user_coin = $doctrine->getRepository('EuroCoinBundle:UserCoin');
		$coins = $user_coin->findBy(array(
			'coin' => $share->getCoinsSuggested(),
			'user' => $share->getFromUser(),
				));

		foreach ($coins as $uc) {
			$uc->removeShare();
		}

		$coins = $user_coin->findBy(array(
			'coin' => $share->getCoinsRequested(),
			'user' => $share->getToUser(),
				));

		foreach ($coins as $uc) {
			$uc->removeShare();
		}

		$doctrine->getManager()->flush();

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
		$shares = $doctrine->getRepository('EuroCoinBundle:Share')->findForUser($user, $all);

		$coin_repo = $doctrine->getRepository('EuroCoinBundle:Coin');
		$coins = array();
		foreach ($shares as $share) {
			$coins[$share->getId()] = array(
				'requested' => $coin_repo->findById($share->getCoinsRequested()),
				'suggested' => $coin_repo->findById($share->getCoinsSuggested()),
			);
		}

		if (!$shares) {
			if (!$all) {
				$flashBag->add('info', 'exchange.none_pending');

				return $this->redirect($this->generateUrl('exchange_list_all'));
			}

			$flashBag->add('info', 'exchange.none');

			return $this->redirect($this->generateUrl('exchange_choose_user'));
		}

		return $this->render('EuroCoinBundle:Exchange:list.html.twig', array(
					'all' => $all,
					'coins' => $coins,
					'shares' => $shares,
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

		$share = new Share();
		$share->setStatus(Share::STATUS_PENDING);
		$share->setFromUser($user);
		$share->setToUser($from);
		$share->setCoinsRequested($from_coins_id);
		$share->setCoinsSuggested($coins_id);

		$from_coins = $doctrine->getRepository('EuroCoinBundle:UserCoin')->findBy(array(
			'coin' => $from_coins_id,
			'user' => $from,
				));

		foreach ($from_coins as $uc) {
			$uc->addShare();
		}

		foreach ($to_coins as $uc) {
			$uc->addShare();
		}

		$em = $doctrine->getManager();
		$em->persist($share);
		$em->flush();

		$this->get('session')->getFlashBag()->add('success', 'coin.doubles.save_successfull');

		return $this->redirect($this->generateUrl('exchange_list'));
	}

}
