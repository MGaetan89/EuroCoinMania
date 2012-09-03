<?php

namespace Euro\PrivateMessageBundle\Controller;

use Euro\PrivateMessageBundle\Entity\Message;
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

		$doctrine = $this->getDoctrine();
		$messages = $doctrine->getRepository('EuroPrivateMessageBundle:Message')->findByConversation($id);

		if (!$messages) {
			$flashBag->add('error', 'pm.not_found');

			return $this->redirect($this->generateUrl('pm_list'));
		}

		$conversation = $messages[0]->getConversation();

		if ($conversation->getFromUser()->getId() !== $user->getId() && $conversation->getToUser()->getId() !== $user->getId()) {
			$flashBag->add('error', 'pm.not_for_you');

			return $this->redirect($this->generateUrl('pm_list'));
		}

		$new_messages = array();
		foreach ($messages as $message) {
			if ($message->isNew()) {
				if (
						($message->getDirection() == Message::DIRECTION_FROM_TO && $conversation->getToUser()->getId() === $user->getId()) ||
						($message->getDirection() == Message::DIRECTION_TO_FROM && $conversation->getFromUser()->getId() === $user->getId())
				) {
					$message->setNew(false);
					$new_messages[] = $message->getId();
				}
			}
		}

		$doctrine->getManager()->flush();

		return $this->render('EuroPrivateMessageBundle:PrivateMessage:read.html.twig', array(
					'messages' => $messages,
					'new_messages' => $new_messages,
				));
	}

}
