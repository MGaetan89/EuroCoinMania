<?php

namespace Euro\PrivateMessageBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class PrivateMessageType extends AbstractType {

	public function buildForm(FormBuilder $builder, array $options) {
		$builder
				->add('to_user')
				->add('title')
				->add('text', 'textarea');
	}

	public function getName() {
		return 'euro_privatemessagetype';
	}

}
