<?php

namespace Euro\CoinBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Validator\ErrorElement;

class CountryAdmin extends Admin {
	protected $translationDomain = 'admin';

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
				->addIdentifier('name', null, array('template' => 'EuroCoinBundle:Admin:Country/list_name.html.twig'))
				->add('join_date')
				->add('exchange_rate', null, array('template' => 'EuroCoinBundle:Admin:Country/list_exchangerate.html.twig'))
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
