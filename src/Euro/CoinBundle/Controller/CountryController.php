<?php

namespace Euro\CoinBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CountryController extends Controller {

	public function listAction() {
		$repository = $this->getDoctrine()->getRepository('EuroCoinBundle:Country');
		$translator = $this->get('translator');
		$countries = $repository->findBy(array(), array('join_date' => 'ASC'));

		usort($countries, function ($a, $b) use ($translator) {
					$a_date = $a->getJoinDate();
					$b_date = $b->getJoinDate();

					if ($a_date != $b_date) {
						return ($a_date < $b_date) ? -1 : 1;
					}

					$a_name = $translator->trans('country.name.' . $a->getName());
					$b_name = $translator->trans('country.name.' . $b->getName());

					return strcmp($a_name, $b_name);
				});

		$countries_js = array();
		foreach ($countries as $country) {
			$name = $translator->trans('country.name.' . $country->getName());
			$countries_js[] = array(
				'name' => $name,
				'nameiso' => $country->getNameIso(),
				'path' => $this->generateUrl('coin_collection', array(
					'country' => $name,
					'id' => $country->getId(),
				)),
			);
		}

		return $this->render('EuroCoinBundle:Country:list.html.twig', array(
					'countries' => $countries,
					'countries_js' => json_encode($countries_js),
				));
	}

}
