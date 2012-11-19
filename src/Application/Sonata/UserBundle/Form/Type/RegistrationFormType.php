<?php

namespace Application\Sonata\UserBundle\Form\Type;

use FOS\UserBundle\Form\Type\RegistrationFormType as BaseType;
use Symfony\Component\Form\FormBuilderInterface;

class RegistrationFormType extends BaseType {

	/**
	 * {@inheritdoc}
	 */
	public function buildForm(FormBuilderInterface $builder, array $options) {
		parent::buildForm($builder, $options);

		$builder
			->add('firstname', null, array('required' => false))
			->add('lastname', null, array('required' => false))
			->add('dateOfBirth', 'birthday', array(
				'required' => false,
				'years' => range(date('Y') - 100,  date('Y')),
			))
			->add('gender', 'choice', array(
				'choices' => array(
					'f' => 'user.genderf',
					'm' => 'user.genderm',
				),
				'required' => false,
			))
			->add('country', 'country', array('required' => false));
	}

	public function getName() {
		return 'application_sonata_user_registration';
	}

}

