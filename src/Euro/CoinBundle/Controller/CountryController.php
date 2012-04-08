<?php

namespace Euro\CoinBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Euro\CoinBundle\Entity\Country;
use Euro\CoinBundle\Form\CountryType;

/**
 * Country controller.
 *
 */
class CountryController extends Controller {

	/**
	 * Lists all Country entities.
	 *
	 */
	public function indexAction() {
		$em = $this->getDoctrine()->getEntityManager();

		$countries = $em->getRepository('EuroCoinBundle:Country')->findAll();

		return $this->render('EuroCoinBundle:Country:index.html.twig', array(
					'countries' => $countries,
				));
	}

	/**
	 * Finds and displays a Country entity.
	 *
	 */
	public function showAction($id) {
		$em = $this->getDoctrine()->getEntityManager();

		$country = $em->getRepository('EuroCoinBundle:Country')->find($id);

		if (!$country) {
			throw $this->createNotFoundException('Unable to find Country entity.');
		}

		$coins = $em->getRepository('EuroCoinBundle:Coin')->getCoinsByCountry($country);
		$counts = array('value' => array(), 'year' => array());
		foreach ($coins as $coin) {
			if (!isset($counts['value'][(string) $coin->getValue()])) {
				$counts['value'][(string) $coin->getValue()] = $coin->getMintage();
			} else {
				$counts['value'][(string) $coin->getValue()] += $coin->getMintage();
			}

			if (!isset($counts['year'][$coin->getYear()])) {
				$counts['year'][$coin->getYear()] = $coin->getMintage();
			} else {
				$counts['year'][$coin->getYear()] += $coin->getMintage();
			}
		}

		return $this->render('EuroCoinBundle:Country:show.html.twig', array(
					'coins' => $coins,
					'country' => $country,
					'counts' => array_sum($counts['value']),
					'counts_value' => $counts['value'],
					'counts_year' => $counts['year'],
				));
	}

	/**
	 * Displays a form to create a new Country entity.
	 *
	 */
	public function newAction() {
		$country = new Country();
		$form = $this->createForm(new CountryType(), $country);

		return $this->render('EuroCoinBundle:Country:new.html.twig', array(
					'country' => $country,
					'form' => $form->createView()
				));
	}

	/**
	 * Creates a new Country entity.
	 *
	 */
	public function createAction() {
		$country = new Country();
		$request = $this->getRequest();
		$form = $this->createForm(new CountryType(), $country);
		$form->bindRequest($request);

		if ($form->isValid()) {
			$em = $this->getDoctrine()->getEntityManager();
			$em->persist($country);
			$em->flush();

			return $this->redirect($this->generateUrl('country_show', array('id' => $country->getId())));
		}

		return $this->render('EuroCoinBundle:Country:new.html.twig', array(
					'country' => $country,
					'form' => $form->createView()
				));
	}

	/**
	 * Displays a form to edit an existing Country entity.
	 *
	 */
	public function editAction($id) {
		$em = $this->getDoctrine()->getEntityManager();

		$country = $em->getRepository('EuroCoinBundle:Country')->find($id);

		if (!$country) {
			throw $this->createNotFoundException('Unable to find Country entity.');
		}

		$editForm = $this->createForm(new CountryType(), $country);

		return $this->render('EuroCoinBundle:Country:edit.html.twig', array(
					'country' => $country,
					'edit_form' => $editForm->createView(),
				));
	}

	/**
	 * Edits an existing Country entity.
	 *
	 */
	public function updateAction($id) {
		$em = $this->getDoctrine()->getEntityManager();

		$country = $em->getRepository('EuroCoinBundle:Country')->find($id);

		if (!$country) {
			throw $this->createNotFoundException('Unable to find Country entity.');
		}

		$editForm = $this->createForm(new CountryType(), $country);

		$request = $this->getRequest();

		$editForm->bindRequest($request);

		if ($editForm->isValid()) {
			$em->persist($country);
			$em->flush();

			return $this->redirect($this->generateUrl('country_edit', array('id' => $id)));
		}

		return $this->render('EuroCoinBundle:Country:edit.html.twig', array(
					'country' => $country,
					'edit_form' => $editForm->createView(),
				));
	}

	/**
	 * Deletes a Country entity.
	 *
	 */
	public function deleteAction($id) {
		$form = $this->createDeleteForm($id);
		$request = $this->getRequest();

		$form->bindRequest($request);

		if ($form->isValid()) {
			$em = $this->getDoctrine()->getEntityManager();
			$country = $em->getRepository('EuroCoinBundle:Country')->find($id);

			if (!$country) {
				throw $this->createNotFoundException('Unable to find Country entity.');
			}

			$em->remove($country);
			$em->flush();
		}

		return $this->redirect($this->generateUrl('country'));
	}

	private function createDeleteForm($id) {
		return $this->createFormBuilder(array('id' => $id))
						->add('id', 'hidden')
						->getForm()
		;
	}

}
