<?php

namespace Euro\PrivateMessageBundle\Controller;

use FOS\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Euro\PrivateMessageBundle\Form\PrivateMessageType;

class PrivateMessageController extends Controller {

	public function hasNewMessageAction() {
		$user = $this->getUser();
		if (!$user instanceof UserInterface) {
			throw new \Exception('You are not allowed to access this page !');
		}

		$em = $this->getDoctrine()->getEntityManager();
		$new_pm = $em->getRepository('EuroPrivateMessageBundle:PrivateMessage')->getNewMessageCount($user);

		return $this->render('EuroPrivateMessageBundle:PrivateMessage:has_new_message.html.twig', array(
					'new_pm' => $new_pm,
				));
	}

	public function indexAction() {
		$user = $this->getUser();
		if (!$user instanceof UserInterface) {
			throw new \Exception('You are not allowed to access this page !');
		}

		$em = $this->getDoctrine()->getEntityManager();
		$conversations = $em->getRepository('EuroPrivateMessageBundle:PrivateMessage')->getConversationsByUser($user);

		return $this->render('EuroPrivateMessageBundle:PrivateMessage:index.html.twig', array(
					'conversations' => $conversations,
				));
	}

	public function newAction() {
		$user = $this->getUser();
		if (!$user instanceof UserInterface) {
			throw new \Exception('You are not allowed to access this page !');
		}

		$form = $this->createForm(new PrivateMessageType());

		return $this->render('EuroPrivateMessageBundle:PrivateMessage:new.html.twig', array(
			'form' => $form->createView(),
		));
	}

	private function getUser() {
		return $this->get('security.context')->getToken()->getUser();
	}

}
