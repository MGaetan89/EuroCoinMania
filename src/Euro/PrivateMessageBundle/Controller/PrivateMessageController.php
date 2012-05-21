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
		$new_pm = $em->getRepository('EuroPrivateMessageBundle:Conversation')->getNewMessageCount($user);

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
		$conversations = $em->getRepository('EuroPrivateMessageBundle:Conversation')->getConversationsByUser($user);

		return $this->render('EuroPrivateMessageBundle:PrivateMessage:index.html.twig', array(
					'conversations' => $conversations,
				));
	}

	/**
	 * @todo Refactoring
	 */
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
				$conversation = new Conversation();
				$conversation->setFromUser($user);
				$conversation->setToUser($form->get('to_user'));
				$conversation->setTitle($form->get('title'));

				$pm = new PrivateMessage();
				$pm->setConversation($conversation);
				$pm->setText($form->getText());
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
		$repository = $em->getRepository('EuroPrivateMessageBundle:Conversation');
		$conversation = $repository->find($id);
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

		$repository->setConversationRead($id, $user);

		return $this->render('EuroPrivateMessageBundle:PrivateMessage:read.html.twig', array(
					'conversation' => $conversation,
					'form' => $form->createView(),
				));
	}

	private function getUser() {
		return $this->get('security.context')->getToken()->getUser();
	}

}
