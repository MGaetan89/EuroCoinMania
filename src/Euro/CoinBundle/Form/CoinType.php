<?php

namespace Euro\CoinBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class CoinType extends AbstractType {

	public function buildForm(FormBuilder $builder, array $options) {
		$builder
				->add('country')
				->add('value', 'choice', array(
					'choices' => array(
						'0.01' => '0.01 €',
						'0.02' => '0.02 €',
						'0.05' => '0.05 €',
						'0.10' => '0.10 €',
						'0.20' => '0.20 €',
						'0.50' => '0.50 €',
						'1.00' => '1.00 €',
						'2.00' => '2.00 €',
					)
				))
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
