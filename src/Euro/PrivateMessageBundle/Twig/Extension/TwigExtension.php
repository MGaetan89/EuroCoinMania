<?php

namespace Euro\PrivateMessageBundle\Twig\Extension;

use Sonata\UserBundle\Model\UserInterface;
use Symfony\Bridge\Doctrine\RegistryInterface;

class TwigExtension extends \Twig_Extension {
	private $doctrine;

	public function __construct(RegistryInterface $doctrine) {
		$this->doctrine = $doctrine;
	}

	public function getFilters() {
		return array(
			'has_pm' => new \Twig_Filter_Method($this, 'filter_has_pm'),
		);
	}

	public function getName() {
		return 'euro_pm_twig';
	}

	public function filter_has_pm(UserInterface $user) {
		return $this->doctrine->getRepository('EuroPrivateMessageBundle:Message')->countNewMessages($user);
	}

}
