<?php

namespace Application\Sonata\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class PreferencesType extends AbstractType {

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
				->add('coins_sort', 'choice', array(
					'choices' => array(
						'asc' => 'user.preferences.coins_sort_ascending',
						'desc' => 'user.preferences.coins_sort_decending',
					),
					'expanded' => true,
					'label' => 'user.preferences.coins_sort',
				))
				->add('show_email', 'choice', array(
					'choices' => array(
						'1' => 'yes',
						'0' => 'no',
					),
					'expanded' => true,
					'label' => 'user.preferences.show_email',
				))
				->add('allow_exchanges', 'choice', array(
					'choices' => array(
						'1' => 'yes',
						'0' => 'no',
					),
					'expanded' => true,
					'label' => 'user.preferences.allow_exchanges',
				))
				->add('exchange_notification', 'choice', array(
					'choices' => array(
						'1' => 'yes',
						'0' => 'no',
					),
					'expanded' => true,
					'label' => 'user.preferences.exchange_notification',
				))
				->add('public_profile', 'choice', array(
					'choices' => array(
						'1' => 'yes',
						'0' => 'no',
					),
					'expanded' => true,
					'label' => 'user.preferences.public_profile',
				));
	}

	public function getName() {
		return 'application_sonata_user_preferences';
	}

}
