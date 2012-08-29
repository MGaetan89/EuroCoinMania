<?php

namespace Euro\CoinBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Show\ShowMapper;
use Sonata\AdminBundle\Validator\ErrorElement;

class CoinAdmin extends Admin {

	protected $translationDomain = 'CoinAdmin';

	protected function configureDatagridFilters(DatagridMapper $filter) {
		$filter
				->add('country')
				->add('value')
				->add('year')
				->add('commemorative');
	}

	protected function configureFormFields(FormMapper $form) {
		$form
				->add('country', 'sonata_type_model')
				->add('value', 'sonata_type_model')
				->add('year', 'sonata_type_model')
				->add('commemorative', null, array('required' => false))
				->add('description', null, array('required' => false))
				->add('mintage');
	}

	protected function configureListFields(ListMapper $listMapper) {
		$listMapper
				->addIdentifier('coin', null, array('template' => 'EuroCoinBundle:Admin:Coin/list_coin.html.twig'))
				->add('commemorative', null, array('editable' => true))
				->add('description')
				->add('mintage', null, array('template' => 'EuroCoinBundle:Admin:Coin/list_mintage.html.twig'))
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
				->add('country')
				->add('value')
				->add('year')
				->add('commemorative')
				->add('description')
				->add('mintage')
				->add('member_total');
	}

	public function prePersist($coin) {
		$coin->setMemberTotal(0);
	}

}
