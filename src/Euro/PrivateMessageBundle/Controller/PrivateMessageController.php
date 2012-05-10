<?php

namespace Euro\PrivateMessageBundle\Controller;

use FOS\UserBundle\Model\UserInterface;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Euro\PrivateMessageBundle\Entity\PrivateMessage;
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

	public function newAction(Request $request) {
		$user = $this->getUser();
		if (!$user instanceof UserInterface) {
			throw new \Exception('You are not allowed to access this page !');
		}

		$pm = new PrivateMessage();
		$em = $this->getDoctrine()->getEntityManager();
		$form = $this->createForm(new PrivateMessageType($em, $user), $pm);

		if ($request->getMethod() === 'POST') {
			$form->bindRequest($request);

			if ($form->isValid()) {
				$pm->setConversation(uniqid());
				$pm->setFromUser($user);
				$pm->setPostDate(new \DateTime());
				$pm->setIsRead(false);

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

	public function readAction($id) {
		$user = $this->getUser();
		if (!$user instanceof UserInterface) {
			throw new \Exception('You are not allowed to access this page !');
		}

		$em = $this->getDoctrine()->getEntityManager();
		$repository = $em->getRepository('EuroPrivateMessageBundle:PrivateMessage');
		$messages = $repository->getConversationById($id);

		$repository->setConversationRead($id, $user);

		return $this->render('EuroPrivateMessageBundle:PrivateMessage:read.html.twig', array(
					'messages' => $messages,
				));
	}

	private function getUser() {
		return $this->get('security.context')->getToken()->getUser();
	}

}
