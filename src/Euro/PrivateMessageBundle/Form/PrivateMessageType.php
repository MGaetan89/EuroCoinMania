<?php

namespace Euro\PrivateMessageBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class PrivateMessageType extends AbstractType {

	public function buildForm(FormBuilder $builder, array $options) {
		$builder
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
