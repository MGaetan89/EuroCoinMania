<?php

namespace Euro\CoinBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Euro\CoinBundle\Entity\Value;
use Euro\CoinBundle\Form\ValueType;

/**
 * Value controller.
 *
 */
class ValueController extends Controller {

	/**
	 * Lists all Value entities.
	 *
	 */
	public function indexAction() {
		$em = $this->getDoctrine()->getEntityManager();

		$values = $em->getRepository('EuroCoinBundle:Value')->findAll();

		return $this->render('EuroCoinBundle:Value:index.html.twig', array(
					'values' => $values
				));
	}

	/**
	 * Finds and displays a Value entity.
	 *
	 */
	public function showAction($id) {
		$em = $this->getDoctrine()->getEntityManager();

		$value = $em->getRepository('EuroCoinBundle:Value')->find($id);

		if (!$value) {
			throw $this->createNotFoundException('Unable to find Value entity.');
		}

		$deleteForm = $this->createDeleteForm($id);

		return $this->render('EuroCoinBundle:Value:show.html.twig', array(
					'value' => $value,
					'delete_form' => $deleteForm->createView(),
				));
	}

	/**
	 * Displays a form to create a new Value entity.
	 *
	 */
	public function newAction() {
		$value = new Value();
		$form = $this->createForm(new ValueType(), $value);

		return $this->render('EuroCoinBundle:Value:new.html.twig', array(
					'value' => $value,
					'form' => $form->createView()
				));
	}

	/**
	 * Creates a new Value entity.
	 *
	 */
	public function createAction() {
		$value = new Value();
		$request = $this->getRequest();
		$form = $this->createForm(new ValueType(), $value);
		$form->bindRequest($request);

		if ($form->isValid()) {
			$em = $this->getDoctrine()->getEntityManager();
			$em->persist($value);
			$em->flush();

			return $this->redirect($this->generateUrl('value_show', array('id' => $value->getId())));
		}

		return $this->render('EuroCoinBundle:Value:new.html.twig', array(
					'value' => $value,
					'form' => $form->createView()
				));
	}

	/**
	 * Displays a form to edit an existing Value entity.
	 *
	 */
	public function editAction($id) {
		$em = $this->getDoctrine()->getEntityManager();

		$value = $em->getRepository('EuroCoinBundle:Value')->find($id);

		if (!$value) {
			throw $this->createNotFoundException('Unable to find Value entity.');
		}

		$editForm = $this->createForm(new ValueType(), $value);
		$deleteForm = $this->createDeleteForm($id);

		return $this->render('EuroCoinBundle:Value:edit.html.twig', array(
					'value' => $value,
					'edit_form' => $editForm->createView(),
					'delete_form' => $deleteForm->createView(),
				));
	}

	/**
	 * Edits an existing Value entity.
	 *
	 */
	public function updateAction($id) {
		$em = $this->getDoctrine()->getEntityManager();

		$value = $em->getRepository('EuroCoinBundle:Value')->find($id);

		if (!$value) {
			throw $this->createNotFoundException('Unable to find Value entity.');
		}

		$editForm = $this->createForm(new ValueType(), $value);
		$deleteForm = $this->createDeleteForm($id);

		$request = $this->getRequest();

		$editForm->bindRequest($request);

		if ($editForm->isValid()) {
			$em->persist($value);
			$em->flush();

			return $this->redirect($this->generateUrl('value_edit', array('id' => $id)));
		}

		return $this->render('EuroCoinBundle:Value:edit.html.twig', array(
					'value' => $value,
					'edit_form' => $editForm->createView(),
					'delete_form' => $deleteForm->createView(),
				));
	}

	/**
	 * Deletes a Value entity.
	 *
	 */
	public function deleteAction($id) {
		$form = $this->createDeleteForm($id);
		$request = $this->getRequest();

		$form->bindRequest($request);

		if ($form->isValid()) {
			$em = $this->getDoctrine()->getEntityManager();
			$value = $em->getRepository('EuroCoinBundle:Value')->find($id);

			if (!$value) {
				throw $this->createNotFoundException('Unable to find Value entity.');
			}

			$em->remove($value);
			$em->flush();
		}

		return $this->redirect($this->generateUrl('value'));
	}

	private function createDeleteForm($id) {
		return $this->createFormBuilder(array('id' => $id))
						->add('id', 'hidden')
						->getForm()
		;
	}

}
