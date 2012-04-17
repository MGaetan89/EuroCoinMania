<?php

namespace Euro\CoinBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Validator\ErrorElement;
use Sonata\AdminBundle\Form\FormMapper;

class ValueAdmin extends Admin {

	protected function configureFormFields(FormMapper $formMapper) {
		$formMapper
				->add('value')
				->add('collector')
		;
	}

	protected function configureDatagridFilters(DatagridMapper $datagridMapper) {
		$datagridMapper
				->add('value')
				->add('collector')
		;
	}

	protected function configureListFields(ListMapper $listMapper) {
		$listMapper
				->addIdentifier('value')
				->add('collector')
		;
	}

	public function validate(ErrorElement $errorElement, $object) {

	}

}
