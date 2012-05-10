<?php

namespace Euro\PrivateMessageBundle\Form;

use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class PrivateMessageType extends AbstractType {
	private $em;
	private $user;

	public function __construct(EntityManager $em, UserInterface $user) {
		$this->em = $em;
		$this->user = $user;
	}

	public function buildForm(FormBuilder $builder, array $options) {
		$em = $this->em;
		$user = $this->user;
		$builder
				->add('to_user', 'entity', array(
					'class' => 'EuroPrivateMessageBundle:PrivateMessage',
					'query_builder' => function() use ($em, $user) {
						return $em->getRepository('EuroUserBundle:User')
								->createQueryBuilder('u')
								->where('u <> :user')
								->orderBy('u.username', 'ASC')
								->setParameter('user', $user);
					}))
				->add('title')
				->add('text', 'textarea');
	}

	public function getDefaultOptions(array $options) {
		return array(
			'data_class' => 'Euro\PrivateMessageBundle\Entity\PrivateMessage',
		);
	}

	public function getName() {
		return 'euro_privatemessagetype';
	}

}
