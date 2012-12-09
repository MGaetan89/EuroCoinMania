<?php

namespace Application\Sonata\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class CustomizeType extends AbstractType {

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
				->add('coins_sort', 'choice', array(
					'choices' => array(
						'asc' => 'user.customize.coins_sort_ascending',
						'desc' => 'user.customize.coins_sort_decending',
					),
					'label' => 'user.customize.coins_sort',
				))
				->add('show_email', 'choice', array(
					'choices' => array(
						'1' => 'yes',
						'0' => 'no',
					),
					'label' => 'user.customize.show_email',
				))
				->add('allow_exchanges', 'choice', array(
					'choices' => array(
						'1' => 'yes',
						'0' => 'no',
					),
					'label' => 'user.customize.allow_exchanges',
				));
	}

	public function getName() {
		return 'application_sonata_user_customize';
	}

}

