<?php

namespace Euro\PrivateMessageBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class MessageType extends AbstractType {

	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
				->add('content', 'sonata_formatter_type', array(
					'event_dispatcher' => $builder->getEventDispatcher(),
					'format_field'   => 'formatter',
					'source_field'   => 'rawContent',
					'target_field'   => 'content'
				));
	}

	public function setDefaultOptions(OptionsResolverInterface $resolver) {
		$resolver->setDefaults(array(
			'data_class' => 'Euro\PrivateMessageBundle\Entity\Message',
		));
	}

	public function getName() {
		return 'euro_privatemessagebundle_messagetype';
	}

}
