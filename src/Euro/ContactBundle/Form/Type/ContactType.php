<?php

namespace Euro\ContactBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;

class ContactType extends AbstractType {

	public function buildForm(FormBuilderInterface $builder, array $options) {
		$builder
				->add('name')
				->add('email', 'email')
				->add('subject', 'choice', array(
					'choices' => array(
						'comment' => 'contact.subject.comment',
						'proposition' => 'contact.subject.proposition',
						'missing_coins' => 'contact.subject.missing_coins',
						'partnership' => 'contact.subject.partnership',
						'bug_report' => 'contact.subject.bug_report',
						'other' => 'contact.subject.other',
					)
				))
				->add('contentFormatter', 'sonata_formatter_type_selector', array(
					'source' => 'message',
					'target' => 'message'
				))
				->add('message', 'textarea');
	}

	public function getName() {
		return 'contact';
	}

}
