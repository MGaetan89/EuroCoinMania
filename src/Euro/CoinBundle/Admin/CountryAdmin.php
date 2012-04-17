<?php

namespace Euro\CoinBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;

class CountryAdmin extends Admin {

	protected function configureFormFields(FormMapper $formMapper) {
		$formMapper
				->add('name')
				->add('nameiso')
				->add('join_date')
				->add('former_currency_iso')
				->add('exchange_rate')
		;
	}

	protected function configureDatagridFilters(DatagridMapper $datagridMapper) {
		$datagridMapper
				->add('name')
				->add('nameiso')
				->add('join_date')
				->add('former_currency_iso')
		;
	}

	protected function configureListFields(ListMapper $listMapper) {
		$listMapper
				->addIdentifier('name')
				->add('nameiso')
				->add('join_date')
				->add('former_currency_iso')
				->add('exchange_rate')
		;
	}

	public function validate(ErrorElement $errorElement, $object) {

	}

}
