<?php

namespace Euro\PrivateMessageBundle\Controller;

use Euro\PrivateMessageBundle\Entity\PrivateMessage;
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

		$pm = new PrivateMessage();
		$em = $this->getDoctrine()->getEntityManager();
		$form = $this->createForm(new PrivateMessageType($em, $user, $to), $pm);

		if ($request->getMethod() === 'POST') {
			$form->bindRequest($request);

			if ($form->isValid()) {
				$pm->setFromUser($user);

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

	/**
	 * @todo Refactoring
	 */
	public function readAction($id, Request $request) {
		$user = $this->getUser();
		if (!$user instanceof UserInterface) {
			throw new \Exception('You are not allowed to access this page !');
		}

		$em = $this->getDoctrine()->getEntityManager();
		$repository = $em->getRepository('EuroPrivateMessageBundle:Conversation');
		$conversation = $repository->find($id);

		$pm = new PrivateMessage();
		$form = $this->createForm(new PrivateMessageType($em, $user), $pm);

		if ($request->getMethod() === 'POST') {
			$form->bindRequest($request);

			if ($form->isValid()) {
				$to_user = $conversation->getToUser();
				if ($to_user->getId() == $user->getId()) {
					$to_user = $conversation->getFromUser();
				}

				$pm->setConversation($conversation->getConversation());
				$pm->setFromUser($user);
				$pm->setToUser($to_user);

				$em->persist($pm);
				$em->flush();

				return $this->redirect($this->generateUrl('pm_read', array('id' => $conversation->getConversation())));
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
