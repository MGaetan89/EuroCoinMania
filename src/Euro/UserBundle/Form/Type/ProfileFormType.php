<?php

namespace Euro\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilder;
use FOS\UserBundle\Form\Type\ProfileFormType as BaseType;

class ProfileFormType extends BaseType {

	public function buildForm(FormBuilder $builder, array $options) {
		parent::buildForm($builder, $options);

		$builder->get('user')
				->remove('username')
				->add('birthday', 'birthday')
		;
	}

	public function getName() {
		return 'euro_user_profile';
	}

}
