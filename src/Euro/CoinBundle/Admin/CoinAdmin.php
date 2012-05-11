<?php

namespace Euro\CoinBundle\Admin;

use Sonata\AdminBundle\Admin\Admin;
use Sonata\AdminBundle\Datagrid\DatagridMapper;
use Sonata\AdminBundle\Datagrid\ListMapper;
use Sonata\AdminBundle\Form\FormMapper;
use Sonata\AdminBundle\Validator\ErrorElement;

class CoinAdmin extends Admin {
	protected $translationDomain = 'admin';

	protected function configureFormFields(FormMapper $formMapper) {
		$formMapper
				->add('value', 'sonata_type_model')
				->add('country', 'sonata_type_model')
				->add('year')
				->add('commemorative')
				->add('mintage')
				->add('description')
		;
	}

	protected function configureDatagridFilters(DatagridMapper $datagridMapper) {
		$datagridMapper
				->add('value')
				->add('country')
				->add('year')
				->add('commemorative')
		;
	}

	protected function configureListFields(ListMapper $listMapper) {
		$listMapper
				->addIdentifier('id')
				->add('value')
				->add('country', null, array('template' => 'EuroCoinBundle:Admin:Coin/list_name.html.twig'))
				->add('year')
				->add('commemorative', null, array('editable' => true))
				->add('mintage', null, array('template' => 'EuroCoinBundle:Admin:Coin/list_mintage.html.twig'))
				->add('description')
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
