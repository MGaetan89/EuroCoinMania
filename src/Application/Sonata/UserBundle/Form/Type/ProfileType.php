<?php

namespace Application\Sonata\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ProfileType extends AbstractType {

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
				->remove('locale');

		$builder
				->add('gender', 'choice', array(
					'choices' => array('m' => 'user.genderm', 'f' => 'user.genderf'),
					'required' => false,
				))
				->add('firstname', null, array('required' => false))
				->add('lastname', null, array('required' => false))
				->add('dateOfBirth', 'birthday', array('required' => false))
				->add('website', null, array('required' => false))
				->add('biography', 'textarea', array('required' => false))
				->add('timezone', 'timezone', array('required' => false))
				->add('phone', null, array('required' => false))
				->add('country', 'country', array('required' => false));
	}

	public function getParent() {
		return 'sonata_user_profile';
	}

	public function getName() {
		return 'application_sonata_user_profile';
	}

}