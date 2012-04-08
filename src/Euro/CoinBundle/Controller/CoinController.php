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
	public function indexAction() {
		$em = $this->getDoctrine()->getEntityManager();

		$coins = $em->getRepository('EuroCoinBundle:Coin')->findAll();

		return $this->render('EuroCoinBundle:Coin:index.html.twig', array(
					'coins' => $coins,
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

		return $this->render('EuroCoinBundle:Coin:show.html.twig', array(
					'coin' => $coin,
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

		return $this->render('EuroCoinBundle:Coin:edit.html.twig', array(
					'coin' => $coin,
					'edit_form' => $editForm->createView(),
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
