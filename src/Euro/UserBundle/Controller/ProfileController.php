<?php

namespace Euro\UserBundle\Controller;

use FOS\UserBundle\Controller\ProfileController as Controller;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

class ProfileController extends Controller {

	public function viewAction($id) {
		$user = $this->container->get('security.context')->getToken()->getUser();
		if (!is_object($user) || !$user instanceof UserInterface) {
			throw new AccessDeniedException('This user does not have access to this section.');
		}

		$em = $this->container->get('doctrine')->getEntityManager();
		if ($user->getId() != $id) {
			$user = $em->getRepository('EuroUserBundle:User')->find($id);
		}

		$total = array(
			'coins' => 0,
			'countries' => array(),
			'doubles' => 0,
		);
		foreach ($user->getCoins() as $coin) {
			$total['coins']++;
			$total['countries'][$coin->getCoin()->getCountry()->getId()] = null;
			if ($coin->getQuantity() > 1) {
				$total['doubles']++;
			}
		}

		$total['countries'] = count($total['countries']);

		$coin_values = array();
		$sorted = array();
		$translator = $this->container->get('translator');
		$user_coins = $em->getRepository('EuroCoinBundle:UserCoin')->getByUser($user);
		foreach ($user_coins as $uc) {
			if ($uc->getQuantity() > 0) {
				$coin = $uc->getCoin();
				$country = $coin->getCountry();
				$value = $coin->getValue();

				$coin_values[$country->getId()][$value->getId()] = (string) $value;
				$sorted[$translator->trans($country)][] = $uc;
			}
		}

		ksort($sorted);

		$coins = array();
		foreach ($sorted as $country) {
			$coins = array_merge($coins, $country);
		}

		foreach ($coin_values as $country => &$cv) {
			rsort($cv);
		}

		return $this->container->get('templating')->renderResponse('EuroUserBundle:Profile:view.html.' . $this->container->getParameter('fos_user.template.engine'), array(
					'coins' => $coins,
					'coin_values' => $coin_values,
					'total' => $total,
					'user' => $user,
				));
	}

}
