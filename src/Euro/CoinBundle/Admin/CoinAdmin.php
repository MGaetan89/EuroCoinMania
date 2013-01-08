<?php

namespace Euro\CoinBundle\Admin;

use Euro\CoinBundle\Entity\Coin;
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
				->add('type', 'doctrine_orm_choice', array(
					'field_options' => array(
						'choices' => Coin::getTypes()
					),
					'field_type' => 'choice',
				));
	}

	protected function configureFormFields(FormMapper $form) {
		$form
				->with('General')
				->add('country', 'sonata_type_model')
				->add('value', 'sonata_type_model')
				->add('year', 'sonata_type_model')
				->add('mintage')
				->end()
				->with('Options', array('collapsed' => true))
				->add('type', 'sonata_type_translatable_choice', array('choices' => Coin::getTypes()))
				->add('description', null, array('required' => false))
				->add('image', 'sonata_type_model_list', array(
					'required' => false
						), array(
					'link_parameters' => array('context' => 'coins')
						)
				)
				->end();
	}

	protected function configureListFields(ListMapper $listMapper) {
		$listMapper
				->addIdentifier('coin', null, array('template' => 'EuroCoinBundle:Admin:Coin/list_coin.html.twig'))
				->add('type', null, array('template' => 'EuroCoinBundle:Admin:Coin/list_type.html.twig'))
				->add('description')
				->add('mintage', null, array('template' => 'EuroCoinBundle:Admin:Coin/list_mintage.html.twig'))
				->add('image')
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
				->add('coin', null, array('template' => 'EuroCoinBundle:Admin:Coin/show_coin.html.twig'))
				->add('type', null, array('template' => 'EuroCoinBundle:Admin:Coin/show_type.html.twig'))
				->add('description')
				->add('mintage', null, array('template' => 'EuroCoinBundle:Admin:Coin/show_mintage.html.twig'))
				->add('image');
	}

	public function prePersist($coin) {
		$coin->setMemberTotal(0);
	}

}
