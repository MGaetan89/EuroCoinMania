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
				->addIdentifier('value', null, array('template' => 'EuroCoinBundle:Admin:Value/list_value.html.twig'))
				->add('collector', null, array('editable' => true))
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
