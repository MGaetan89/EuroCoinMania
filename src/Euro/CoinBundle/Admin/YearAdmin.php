<?php

namespace Euro\CoinBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Validator\ErrorElement;

class YearAdmin extends Admin {
	protected $translationDomain = 'YearAdmin';

	protected function configureDatagridFilters(DatagridMapper $filter) {
		$filter
				->add('year')
				->add('workshop');
	}

	protected function configureFormFields(FormMapper $form) {
		$form
				->add('year', null, array('attr' => array(
						'max' => date('Y'),
						'min' => 1999,
						)))
				->add('workshop', 'sonata_type_model', array('required' => false));
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
					),
				));
	}

	protected function configureShowFields(ShowMapper $show) {
		$show
				->add('year')
				->add('workshop');
	}

	public function validate(ErrorElement $errorElement, $object) {
		$errorElement
				->with('year')
				->assertMin(array('limit' => 1999))
				->assertMax(array('limit' => date('Y')))
				->end();
	}

}
