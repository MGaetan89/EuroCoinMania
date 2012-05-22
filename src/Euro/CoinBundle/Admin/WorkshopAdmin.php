<?php

namespace Euro\CoinBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Validator\ErrorElement;

class WorkshopAdmin extends Admin {
	protected $translationDomain = 'admin';

	protected function configureFormFields(FormMapper $formMapper) {
		$formMapper
				->add('short_name')
				->add('name')
		;
	}

	protected function configureDatagridFilters(DatagridMapper $datagridMapper) {
		$datagridMapper
				->add('short_name')
				->add('name')
		;
	}

	protected function configureListFields(ListMapper $listMapper) {
		$listMapper
				->addIdentifier('short_name')
				->add('name')
				->add('_action', 'actions', array(
					'actions' => array(
						'view' => array(),
						'edit' => array(),
						'delete' => array(),
					)
				))
		;
	}

	public function validate(ErrorElement $errorElement, $object) {

	}

}
