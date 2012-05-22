<?php

namespace Euro\CoinBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Validator\ErrorElement;

class YearAdmin extends Admin {
	protected $translationDomain = 'admin';

	protected function configureFormFields(FormMapper $formMapper) {
		$formMapper
				->add('year')
				->add('workshop', 'sonata_type_model', array('required' => false))
		;
	}

	protected function configureDatagridFilters(DatagridMapper $datagridMapper) {
		$datagridMapper
				->add('year')
				->add('workshop')
		;
	}

	protected function configureListFields(ListMapper $listMapper) {
		$listMapper
				->addIdentifier('year')
				->add('workshop')
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
