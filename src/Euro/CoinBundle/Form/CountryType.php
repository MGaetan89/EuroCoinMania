<?php

namespace Euro\CoinBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilder;

class CountryType extends AbstractType {

	public function buildForm(FormBuilder $builder, array $options) {
		$builder
				->add('name', null, array(
					'attr' => array(
						'value' => $options['data']->getName(false),
					),
				))
				->add('nameiso')
				->add('join_date', null, array(
					'years' => range('2002', date('Y')),
				))
				->add('former_currency_iso')
				->add('exchange_rate', null, array(
					'precision' => 10,
				))
		;
	}

	public function getName() {
		return 'euro_coinbundle_countrytype';
	}

}
