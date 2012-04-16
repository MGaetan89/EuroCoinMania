<?php

namespace Euro\CoinBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Euro\CoinBundle\Entity\Coin;
use Euro\CoinBundle\Form\CoinType;

/**
 * Coin controller.
 *
 */
class CoinController extends Controller {

	/**
	 * Lists all Coin entities.
	 *
	 */
	public function indexAction($country, $year, $value, $commemorative) {
		$em = $this->getDoctrine()->getEntityManager();
		$translator = $this->get('translator');

		$filters = array(
			'commemorative' => $commemorative,
			'country' => $country,
			'value' => $value,
			'year' => $year,
		);
		$coins = $em->getRepository('EuroCoinBundle:Coin')->getCoinsByFilters($filters);
		$coin_values = array();
		$commemoratives = array();
		$countries = array();
		$values = array();
		$years = array();
		foreach ($coins as $coin) {
			$country = $coin->getCountry();
			$value = $coin->getValue();

			$coin_values[$country->getId()][$value->getId()] = (string) $value;
			$commemoratives[$coin->getCommemorative()] = $coin->getCommemorative();
			$countries[$country->getId()] = $translator->trans($coin->getCountry());
			$values[$value->getId()] = (string) $value;
			$years[$coin->getYear()] = $coin->getYear();
		}

		asort($countries);
		asort($values);
		sort($years);
		foreach ($coin_values as $country => &$val) {
			rsort($val);
		}

		return $this->render('EuroCoinBundle:Coin:index.html.twig', array(
					'coin_values' => $coin_values,
					'coins' => $coins,
					'commemoratives' => count($commemoratives) > 1,
					'countries' => (count($countries) > 1) ? $countries : array(),
					'filters' => $filters,
					'values' => (count($values) > 1) ? $values : array(),
					'years' => (count($years) > 1) ? $years : array(),
				));
	}

	/**
	 * Finds and displays a Coin entity.
	 *
	 */
	public function showAction($id) {
		$em = $this->getDoctrine()->getEntityManager();

		$coin = $em->getRepository('EuroCoinBundle:Coin')->find($id);

		if (!$coin) {
			throw $this->createNotFoundException('Unable to find Coin entity.');
		}

		$deleteForm = $this->createDeleteForm($id);

		return $this->render('EuroCoinBundle:Coin:show.html.twig', array(
					'coin' => $coin,
					'delete_form' => $deleteForm->createView(),
				));
	}

	/**
	 * Displays a form to create a new Coin entity.
	 *
	 */
	public function newAction() {
		$coin = new Coin();
		$form = $this->createForm(new CoinType(), $coin);

		return $this->render('EuroCoinBundle:Coin:new.html.twig', array(
					'coin' => $coin,
					'form' => $form->createView()
				));
	}

	/**
	 * Creates a new Coin entity.
	 *
	 */
	public function createAction() {
		$coin = new Coin();
		$request = $this->getRequest();
		$form = $this->createForm(new CoinType(), $coin);
		$form->bindRequest($request);

		if ($form->isValid()) {
			$em = $this->getDoctrine()->getEntityManager();
			$em->persist($coin);
			$em->flush();

			return $this->redirect($this->generateUrl('coin_show', array('id' => $coin->getId())));
		}

		return $this->render('EuroCoinBundle:Coin:new.html.twig', array(
					'coin' => $coin,
					'form' => $form->createView()
				));
	}

	/**
	 * Displays a form to edit an existing Coin entity.
	 *
	 */
	public function editAction($id) {
		$em = $this->getDoctrine()->getEntityManager();

		$coin = $em->getRepository('EuroCoinBundle:Coin')->find($id);

		if (!$coin) {
			throw $this->createNotFoundException('Unable to find Coin entity.');
		}

		$editForm = $this->createForm(new CoinType(), $coin);
		$deleteForm = $this->createDeleteForm($id);

		return $this->render('EuroCoinBundle:Coin:edit.html.twig', array(
					'coin' => $coin,
					'edit_form' => $editForm->createView(),
					'delete_form' => $deleteForm->createView(),
				));
	}

	/**
	 * Edits an existing Coin entity.
	 *
	 */
	public function updateAction($id) {
		$em = $this->getDoctrine()->getEntityManager();

		$coin = $em->getRepository('EuroCoinBundle:Coin')->find($id);

		if (!$coin) {
			throw $this->createNotFoundException('Unable to find Coin entity.');
		}

		$editForm = $this->createForm(new CoinType(), $coin);
		$deleteForm = $this->createDeleteForm($id);

		$request = $this->getRequest();

		$editForm->bindRequest($request);

		if ($editForm->isValid()) {
			$em->persist($coin);
			$em->flush();

			return $this->redirect($this->generateUrl('coin_edit', array('id' => $id)));
		}

		return $this->render('EuroCoinBundle:Coin:edit.html.twig', array(
					'coin' => $coin,
					'edit_form' => $editForm->createView(),
					'delete_form' => $deleteForm->createView(),
				));
	}

	/**
	 * Deletes a Coin entity.
	 *
	 */
	public function deleteAction($id) {
		$form = $this->createDeleteForm($id);
		$request = $this->getRequest();

		$form->bindRequest($request);

		if ($form->isValid()) {
			$em = $this->getDoctrine()->getEntityManager();
			$coin = $em->getRepository('EuroCoinBundle:Coin')->find($id);

			if (!$coin) {
				throw $this->createNotFoundException('Unable to find Coin entity.');
			}

			$em->remove($coin);
			$em->flush();
		}

		return $this->redirect($this->generateUrl('coin'));
	}

	private function createDeleteForm($id) {
		return $this->createFormBuilder(array('id' => $id))
						->add('id', 'hidden')
						->getForm()
		;
	}

}
