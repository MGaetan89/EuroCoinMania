<?php

namespace Euro\ContactBundle\Controller;

use Euro\ContactBundle\Form\Type\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class ContactController extends Controller {

	public function contactAction(Request $request) {
		$default = array(
			'email' => null,
			'message' => null,
			'name' => null,
			'subject' => $request->get('subject') ? : 'other',
		);
		$form = $this->createForm(new ContactType(), $default);

		if ($request->isMethod('POST')) {
			$form->bind($request);
			$data = $form->getData();
			$translator = $this->get('translator');

			$subject = 'contact.subject.' . $data['subject'];
			if ($translator->trans($subject) !== $subject) {
				$subject = $translator->trans($subject);
			}

			$message = \Swift_Message::newInstance()
					->setSubject($subject)
					->setFrom($data['email'])
					->setTo('contact@eurocoin-mania.eu')
					->setBody($data['message']);

			if ($this->get('mailer')->send($message)) {
				$this->get('session')->getFlashBag()->add('success', 'contact.send.success');
			} else {
				$this->get('session')->getFlashBag()->add('error', 'contact.send.fail');
			}
		}

		return $this->render('EuroContactBundle:Contact:contact.html.twig', array(
					'form' => $form->createView(),
				));
	}

}
