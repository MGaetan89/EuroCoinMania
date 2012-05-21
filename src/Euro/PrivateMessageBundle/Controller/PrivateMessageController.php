<?php

namespace Euro\PrivateMessageBundle\Controller;

use Euro\PrivateMessageBundle\Entity\Conversation;
use Euro\PrivateMessageBundle\Entity\PrivateMessage;
use Euro\PrivateMessageBundle\Form\ConversationType;
use Euro\PrivateMessageBundle\Form\PrivateMessageType;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class PrivateMessageController extends Controller {

	public function closeAction($id) {
		$user = $this->getUser();
		if (!$user instanceof UserInterface) {
			throw new \Exception('You are not allowed to access this page !');
		}

		$em = $this->getDoctrine()->getEntityManager();
		$repository = $em->getRepository('EuroPrivateMessageBundle:Conversation');
		$conversation = $repository->find($id);
		if ($repository->closeConversation($id, $user)) {
			$em->getRepository('EuroCoinBundle:Share')->cancelShare($conversation, $user);
		}

		return $this->redirect($this->generateUrl('pm_read', array('id' => $id)));
	}

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

		$user_id = $user->getId();
		$em = $this->getDoctrine()->getEntityManager();
		$conversations = $em->getRepository('EuroPrivateMessageBundle:Conversation')->getConversationsByUser($user);

		$unread = array();
		foreach ($conversations as $conversation) {
			$from = $conversation->getFromUser()->getId();
			$to = $conversation->getToUser()->getId();
			foreach ($conversation->getPm() as $pm) {
				if (!$pm->getIsRead()) {
					$direction = $pm->getDirection();
					if (
							($to === $user_id && $direction === PrivateMessage::DIRECTION_FROM_TO) ||
							($from === $user_id && $direction === PrivateMessage::DIRECTION_TO_FROM)
					) {
						$unread[$conversation->getId()] = true;
					}
				}
			}
		}

		return $this->render('EuroPrivateMessageBundle:PrivateMessage:index.html.twig', array(
					'conversations' => $conversations,
					'unread' => $unread,
				));
	}

	public function newAction($to, Request $request) {
		$user = $this->getUser();
		if (!$user instanceof UserInterface) {
			throw new \Exception('You are not allowed to access this page !');
		}

		$em = $this->getDoctrine()->getEntityManager();
		$form = $this->createForm(new ConversationType($em, $user, $to));

		if ($request->getMethod() === 'POST') {
			$form->bindRequest($request);

			if ($form->isValid()) {
				$data = (object) $form->getData();

				$conversation = new Conversation();
				$conversation->setFromUser($user);
				$conversation->setToUser($data->to_user);
				$conversation->setTitle($data->title);

				$pm = new PrivateMessage();
				$pm->setConversation($conversation);
				$pm->setText($data->text);
				$pm->setDirection(PrivateMessage::DIRECTION_FROM_TO);

				$em->persist($conversation);
				$em->persist($pm);
				$em->flush();

				$this->get('session')->setFlash('notice', $this->get('translator')->trans('pm.message_sent'));

				return $this->redirect($this->generateUrl('pm_index'));
			}
		}

		return $this->render('EuroPrivateMessageBundle:PrivateMessage:new.html.twig', array(
					'form' => $form->createView(),
				));
	}

	public function readAction($id, Request $request) {
		$user = $this->getUser();
		if (!$user instanceof UserInterface) {
			throw new \Exception('You are not allowed to access this page !');
		}

		$em = $this->getDoctrine()->getEntityManager();
		$conversation = $em->getRepository('EuroPrivateMessageBundle:Conversation')->find($id);
		$user_id = $user->getId();
		if ($conversation->getFromUser()->getId() !== $user_id && $conversation->getToUser()->getId() !== $user_id) {
			throw new \Exception('You are not allowed to access this page !');
		}

		$pm = new PrivateMessage();
		$form = $this->createForm(new PrivateMessageType(), $pm);

		if ($request->getMethod() === 'POST') {
			$form->bindRequest($request);

			if ($form->isValid()) {
				$pm->setConversation($conversation);
				if ($conversation->getFromUser()->getId() === $user->getId()) {
					$pm->setDirection(PrivateMessage::DIRECTION_FROM_TO);
				} else {
					$pm->setDirection(PrivateMessage::DIRECTION_TO_FROM);
				}

				$em->persist($pm);
				$em->flush();

				return $this->redirect($this->generateUrl('pm_read', array('id' => $id)));
			}
		}

		if ($conversation) {
			$pm = $conversation->getPm();
			$from = $conversation->getFromUser()->getId();
			$to = $conversation->getToUser()->getId();
			if (!$pm[0]->getIsRead()) {
				$direction = $pm[0]->getDirection();
				if (
						($direction === PrivateMessage::DIRECTION_FROM_TO && $to === $user_id) ||
						($direction === PrivateMessage::DIRECTION_TO_FROM && $from === $user_id)
				) {
					$em->getRepository('EuroPrivateMessageBundle:PrivateMessage')->setConversationRead($conversation);
				}
			}
		}

		return $this->render('EuroPrivateMessageBundle:PrivateMessage:read.html.twig', array(
					'conversation' => $conversation,
					'form' => $form->createView(),
				));
	}

	private function getUser() {
		return $this->get('security.context')->getToken()->getUser();
	}

}
