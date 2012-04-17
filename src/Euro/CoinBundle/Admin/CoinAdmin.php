<?php

namespace Euro\CoinBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;

class CoinAdmin extends Admin {

	protected function configureFormFields(FormMapper $formMapper) {
		$formMapper
				->add('value')
				->add('country')
				->add('year')
				->add('commemorative')
				->add('mintage')
				->add('description')
		;
	}

	protected function configureDatagridFilters(DatagridMapper $datagridMapper) {
		$datagridMapper
				->add('value')
				->add('country')
				->add('year')
				->add('commemorative')
		;
	}

	protected function configureListFields(ListMapper $listMapper) {
		$listMapper
				->add('value')
				->add('country')
				->add('year')
				->add('commemorative')
				->add('mintage')
				->add('description')
		;
	}

	public function validate(ErrorElement $errorElement, $object) {

	}

}
