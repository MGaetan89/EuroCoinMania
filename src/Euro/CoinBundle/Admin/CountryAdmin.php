<?php

namespace Euro\CoinBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Validator\ErrorElement;

class CountryAdmin extends Admin {

	protected $translationDomain = 'CountryAdmin';

	protected function configureDatagridFilters(DatagridMapper $filter) {
		$filter
				->add('name')
				->add('name_iso')
				->add('join_date')
				->add('former_currency_iso');
	}

	protected function configureFormFields(FormMapper $form) {
		$form
				->add('name')
				->add('name_iso')
				->add('join_date', null, array('years' => range(1999, date('Y'))))
				->add('former_currency_iso')
				->add('exchange_rate');
	}

	protected function configureListFields(ListMapper $listMapper) {
		$listMapper
				->addIdentifier('name', null, array('template' => 'EuroCoinBundle:Admin:Country/list_name.html.twig'))
				->add('join_date')
				->add('exchange_rate', null, array('template' => 'EuroCoinBundle:Admin:Country/list_exchange_rate.html.twig'))
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
				->add('name', null, array('template' => 'EuroCoinBundle:Admin:Country/show_name.html.twig'))
				->add('join_date')
				->add('exchange_rate', null, array('template' => 'EuroCoinBundle:Admin:Country/show_exchange_rate.html.twig'));
	}

	public function validate(ErrorElement $errorElement, $object) {
		$errorElement
				->with('name')
					->assertMaxLength(array('limit' => 25))
				->end()
				->with('name_iso')
					->assertMinLength(array('limit' => 2))
					->assertMaxLength(array('limit' => 2))
				->end()
				->with('former_currency_iso')
					->assertMinLength(array('limit' => 3))
					->assertMaxLength(array('limit' => 3))
				->end();
	}

}
