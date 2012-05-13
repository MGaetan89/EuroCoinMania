<?php

namespace Euro\PrivateMessageBundle\Form;

use Doctrine\ORM\EntityManager;
use FOS\UserBundle\Model\UserInterface;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class PrivateMessageType extends AbstractType {
	private $em;
	private $to;
	private $user;

	public function __construct(EntityManager $em, UserInterface $user, $to = 0) {
		$this->em = $em;
		$this->to = $to;
		$this->user = $user;
	}

	public function buildForm(FormBuilder $builder, array $options) {
		$em = $this->em;
		$to = $this->to > 0;
		$user = ($to) ? $this->to : $this->user;
		$builder
				->add('to_user', 'entity', array(
					'class' => 'EuroPrivateMessageBundle:PrivateMessage',
					'query_builder' => function() use ($em, $user, $to) {
						$query_builder = $em->getRepository('EuroUserBundle:User')
								->createQueryBuilder('u')
								->orderBy('u.username', 'ASC');

						if ($to) {
							$query_builder->where('u = :user');
						} else {
							$query_builder->where('u <> :user');
						}

						return $query_builder->setParameter('user', $user);
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
