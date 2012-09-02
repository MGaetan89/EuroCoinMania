<?php

namespace Euro\PrivateMessageBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PrivateMessageController extends Controller {

	public function listAction($archives) {
		$flashBag = $this->get('session')->getFlashBag();
		if (!$user = $this->getUser()) {
			$flashBag->add('error', 'user.login_required');

			return $this->redirect($this->generateUrl('fos_user_security_login'));
		}

		$conversations = $this->getDoctrine()->getRepository('EuroPrivateMessageBundle:Conversation')->findConversationsForUser($user, $archives);

		return $this->render('EuroPrivateMessageBundle:PrivateMessage:list.html.twig', array(
					'archives' => $archives,
					'conversations' => $conversations,
				));
	}

	public function readAction($id) {
		$flashBag = $this->get('session')->getFlashBag();
		if (!$user = $this->getUser()) {
			$flashBag->add('error', 'user.login_required');

			return $this->redirect($this->generateUrl('fos_user_security_login'));
		}

		$messages = $this->getDoctrine()->getRepository('EuroPrivateMessageBundle:Message')->findByConversation($id);

		if (!$messages) {
			// Cette conversation n'existe pas
		}

		$conversation = $messages[0]->getConversation();

		if ($conversation->getFromUser() !== $user || $conversation->getToUser() !== $user) {
			// Cette conversation ne vous concerne pas !
		}

		return $this->render('EuroPrivateMessageBundle:PrivateMessage:read.html.twig', array(
					'messages' => $messages,
				));
	}

}
