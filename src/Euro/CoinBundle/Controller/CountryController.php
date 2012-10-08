<?php

namespace Euro\CoinBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class CountryController extends Controller {

	/**
	 * List all the countries
	 * @return \Symfony\Component\HttpFoundation\Response 
	 */
	public function listAction() {
		$repository = $this->getDoctrine()->getRepository('EuroCoinBundle:Country');
		$translator = $this->get('translator');
		$countries = $repository->findAll();

		// Sort the countries by translated name
		$collator = new \Collator($this->getRequest()->getLocale());
		usort($countries, function ($a, $b) use ($collator, $translator) {
					$a_date = $a->getJoinDate();
					$b_date = $b->getJoinDate();

					if ($a_date != $b_date) {
						return ($a_date < $b_date) ? -1 : 1;
					}

					$a_name = $translator->trans((string) $a);
					$b_name = $translator->trans((string) $b);

					return $collator->compare($a_name, $b_name);
				});

		$countries_js = array();
		foreach ($countries as $country) {
			$name = $translator->trans((string) $country);
			$countries_js[] = array(
				'name' => $name,
				'nameiso' => $country->getNameIso(),
				'path' => $this->generateUrl('coin_collection1', array(
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
