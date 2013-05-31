<?php

namespace Euro\PrivateMessageBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ConversationType extends AbstractType {

	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
				->add('to_user', 'text', array('virtual' => true))
				->add('title')
				->add('contentFormatter', 'sonata_formatter_type_selector', array(
					'source' => 'content',
					'target' => 'content'
				))
				->add('content', 'textarea');
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'data_class' => 'Euro\PrivateMessageBundle\Entity\Conversation',
		));
	}

	public function getName() {
		return 'euro_privatemessagebundle_conversationtype';
	}

}
