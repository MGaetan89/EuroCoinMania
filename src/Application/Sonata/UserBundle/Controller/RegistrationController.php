<?php

namespace Application\Sonata\UserBundle\Controller;

use FOS\UserBundle\Controller\RegistrationController as BaseController;
use FOS\UserBundle\Event\FilterUserResponseEvent;
use FOS\UserBundle\Event\FormEvent;
use FOS\UserBundle\Event\UserEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

class RegistrationController extends BaseController {

	public function confirmedAction() {
		$user = $this->container->get('security.context')->getToken()->getUser();

		if (is_object($user) && $user instanceof UserInterface) {
			$this->container->get('session')->getFlashBag()->add('success', 'user.registration_success');
		}

		return new RedirectResponse($this->container->get('router')->generate('welcome'));
	}

	public function registerAction(Request $request) {
		$formFactory = $this->container->get('fos_user.registration.form.factory');
		$userManager = $this->container->get('fos_user.user_manager');
		$dispatcher = $this->container->get('event_dispatcher');

		$user = $userManager->createUser();
		$user->setEnabled(true);

		$dispatcher->dispatch(FOSUserEvents::REGISTRATION_INITIALIZE, new UserEvent($user, $request));

		$form = $formFactory->createForm();
		$form->setData($user);

		if ('POST' === $request->getMethod()) {
			$form->bind($request);

			if ($form->isValid()) {
				$event = new FormEvent($form, $request);
				$dispatcher->dispatch(FOSUserEvents::REGISTRATION_SUCCESS, $event);

				$userManager->updateUser($user);

				if (null === $response = $event->getResponse()) {
					$url = $this->container->get('router')->generate('fos_user_registration_confirmed');
					$response = new RedirectResponse($url);
				}

				$dispatcher->dispatch(FOSUserEvents::REGISTRATION_COMPLETED, new FilterUserResponseEvent($user, $request, $response));

				return $response;
			}
		}

		$tplfile = 'FOSUserBundle:Registration:register.html.';
		if ($this->container->get('request')->get('_route') === '_internal') {
			$tplfile = 'FOSUserBundle:Registration:register_content.html.';
		}

		return $this->container->get('templating')->renderResponse($tplfile . $this->getEngine(), array(
					'form' => $form->createView(),
				));
	}

}
