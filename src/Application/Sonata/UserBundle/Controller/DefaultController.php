<?php

namespace Application\Sonata\UserBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller {

	public function collectionAction($id) {
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

		return $this->render('ApplicationSonataUserBundle:Profile:collection.html.twig', array(
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
