<?php

namespace Euro\CoinBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class CoinType extends AbstractType {

	public function buildForm(FormBuilder $builder, array $options) {
		$builder
				->add('country')
				->add('value')
				->add('year')
				->add('commemorative')
				->add('mintage')
				->add('description')
		;
	}

	public function getName() {
		return 'euro_coinbundle_cointype';
	}

}
