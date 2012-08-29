<?php

namespace Euro\CoinBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Validator\ErrorElement;

class WorkshopAdmin extends Admin {

	protected $translationDomain = 'WorkshopAdmin';

	protected function configureDatagridFilters(DatagridMapper $filter) {
		$filter
				->add('short_name')
				->add('name');
	}

	protected function configureFormFields(FormMapper $form) {
		$form
				->add('short_name')
				->add('name');
	}

	protected function configureListFields(ListMapper $listMapper) {
		$listMapper
				->addIdentifier('name', null, array('template' => 'EuroCoinBundle:Admin:Workshop/list_name.html.twig'))
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
				->add('short_name')
				->add('name');
	}

}
