<?php

namespace Euro\CoinBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Validator\ErrorElement;

class ValueAdmin extends Admin {

	protected $translationDomain = 'ValueAdmin';

	protected function configureDatagridFilters(DatagridMapper $filter) {
		$filter
				->add('value');
	}

	protected function configureFormFields(FormMapper $form) {
		$form
				->add('value');
	}

	protected function configureListFields(ListMapper $listMapper) {
		$listMapper
				->addIdentifier('value', null, array('template' => 'EuroCoinBundle:Admin:Value/list_value.html.twig'))
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
				->add('value', null, array('template' => 'EuroCoinBundle:Admin:Value/show_value.html.twig'));
	}

}
