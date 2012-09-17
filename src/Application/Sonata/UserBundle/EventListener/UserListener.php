<?php

namespace Application\Sonata\UserBundle\EventListener;

use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\HttpKernel\Event\GetResponseEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class UserListener {

	/**
	 * kernel.request event. If a guest user doesn't have an opened session, locale is equal to
	 * "undefined" as configured by default in parameters.ini. If so, set as a locale the user's
	 * preferred language.
	 *
	 * @param \Symfony\Component\HttpKernel\Event\GetResponseEvent $event
	 */
	public function setLocaleForUnauthenticatedUser(GetResponseEvent $event) {
		if (HttpKernelInterface::MASTER_REQUEST !== $event->getRequestType()) {
			return;
		}

		$request = $event->getRequest();
		if ($request->getLocale() == 'undefined') {
			$request->setLocale($request->getPreferredLanguage());
		}
	}

	/**
	 * security.interactive_login event. If a user chose a locale in preferences, it would be set,
	 * if not, a locale that was set by setLocaleForUnauthenticatedUser remains.
	 *
	 * @param \Symfony\Component\Security\Http\Event\InteractiveLoginEvent $event
	 */
	public function setLocaleForAuthenticatedUser(InteractiveLoginEvent $event) {
		/** @var \Application\Sonata\UserBundle\Entity\User $user  */
		$user = $event->getAuthenticationToken()->getUser();

		if ($user->getLocale()) {
			$event->getRequest()->setLocale($user->getLocale());
		}
	}

}
