<?php

namespace Euro\ContactBundle\Controller;

use Euro\ContactBundle\Form\Type\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class ContactController extends Controller {

	public function contactAction() {
		$form = $this->createForm(new ContactType());

		return $this->render('EuroContactBundle:Contact:index.html.twig', array(
					'form' => $form->createView(),
				));
	}

}
