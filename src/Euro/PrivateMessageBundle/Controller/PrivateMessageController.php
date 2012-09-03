<?php

namespace Euro\PrivateMessageBundle\Controller;

use Euro\PrivateMessageBundle\Entity\Conversation;
use Euro\PrivateMessageBundle\Entity\Message;
use Euro\PrivateMessageBundle\Form\ConversationType;
use Euro\PrivateMessageBundle\Form\MessageType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class PrivateMessageController extends Controller {

	public function answerProcessAction($id) {
		$translator = $this->get('translator');

		if (!$user = $this->getUser()) {
			throw $this->createNotFoundException($translator->trans('user.login_required'));
		}

		$conversation = $this->getDoctrine()->getRepository('EuroPrivateMessageBundle:Conversation')->find($id);

		if (!$conversation) {
			throw $this->createNotFoundException($translator->trans('pm.not_found'));
		}

		if ($conversation->getFromUser()->getId() !== $user->getId() && $conversation->getToUser()->getId() !== $user->getId()) {
			throw $this->createNotFoundException($translator->trans('pm.not_for_you'));
		}

		$message = new Message();
		$form = $this->createForm(new MessageType(), $message);
		$request = $this->getRequest();

		if ($request->getMethod() == 'POST') {
			$form->bind($request);

			if ($form->isValid()) {
				if ($conversation->getFromUser()->getId() === $user->getId()) {
					$message->setDirection(Message::DIRECTION_FROM_TO);
				} else {
					$message->setDirection(Message::DIRECTION_TO_FROM);
				}

				$message->setConversation($conversation);

				$em = $this->getDoctrine()->getManager();
				$em->persist($message);
				$em->flush();

				return $this->redirect($this->generateUrl('pm_read', array(
									'id' => $conversation->getId(),
									'title' => $conversation->getTitle(),
								)));
			}
		}

		return $this->redirect($this->generateUrl('pm_write'));
	}

	public function closeAction($id) {
		$flashBag = $this->get('session')->getFlashBag();
		if (!$user = $this->getUser()) {
			$flashBag->add('error', 'user.login_required');

			return $this->redirect($this->generateUrl('fos_user_security_login'));
		}

		$doctrine = $this->getDoctrine();
		$conversation = $doctrine->getRepository('EuroPrivateMessageBundle:Conversation')->find($id);

		if (!$conversation) {
			$flashBag->add('error', 'pm.not_found');

			return $this->redirect($this->generateUrl('pm_list'));
		}

		if ($conversation->getFromUser()->getId() !== $user->getId() && $conversation->getToUser()->getId() !== $user->getId()) {
			$flashBag->add('error', 'pm.not_for_you');

			return $this->redirect($this->generateUrl('pm_list'));
		}

		$message = new Message();
		$message->setContent('pm.text.conversation_closed');
		$message->setType(Message::TYPE_INFO);
		$message->setConversation($conversation);

		if ($conversation->getFromUser()->getId() === $user->getId()) {
			$message->setDirection(Message::DIRECTION_FROM_TO);
		} else {
			$message->setDirection(Message::DIRECTION_TO_FROM);
		}

		$em = $doctrine->getManager();
		$em->persist($message);

		$conversation->setOpen(false);
		$conversation->addMessage($message);

		$em->flush();

		$flashBag->add('success', 'pm.closed_success');

		return $this->redirect($this->generateUrl('pm_read', array(
							'id' => $conversation->getId(),
							'title' => $this->get('translator')->trans($conversation->getTitle()),
						)));
	}

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

		$form = $this->createForm(new MessageType());

		return $this->render('EuroPrivateMessageBundle:PrivateMessage:read.html.twig', array(
					'form' => $form->createView(),
					'messages' => $messages,
					'new_messages' => $new_messages,
				));
	}

	public function writeAction() {
		$flashBag = $this->get('session')->getFlashBag();
		if (!$user = $this->getUser()) {
			$flashBag->add('error', 'user.login_required');

			return $this->redirect($this->generateUrl('fos_user_security_login'));
		}

		$form = $this->createForm(new ConversationType());

		return $this->render('EuroPrivateMessageBundle:PrivateMessage:write.html.twig', array(
					'form' => $form->createView(),
				));
	}

	public function writeProcessAction() {
		$translator = $this->get('translator');

		if (!$user = $this->getUser()) {
			throw $this->createNotFoundException($translator->trans('user.login_required'));
		}

		$form = $this->createForm(new ConversationType());
		$request = $this->getRequest();

		if ($request->getMethod() == 'POST') {
			$form->bind($request);

			if ($form->isValid()) {
				$data = $request->request->get('euro_privatemessagebundle_conversationtype');

				$content = $data['message'];
				$title = $data['title'];
				$to_user = $this->getDoctrine()->getRepository('ApplicationSonataUserBundle:User')->findOneByUsername($data['to_user']);

				$conversation = new Conversation();
				$conversation->setFromUser($user);
				$conversation->setToUser($to_user);
				$conversation->setTitle($title);

				$message = new Message();
				$message->setContent($content);
				$message->setConversation($conversation);

				$conversation->addMessage($message);

				$em = $this->getDoctrine()->getManager();
				$em->persist($conversation);
				$em->persist($message);
				$em->flush();

				return $this->redirect($this->generateUrl('pm_read', array(
									'id' => $conversation->getId(),
									'title' => $title,
								)));
			}
		}

		return $this->redirect($this->generateUrl('pm_write'));
	}

}
