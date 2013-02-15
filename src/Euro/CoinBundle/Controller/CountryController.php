<?php

namespace Euro\CoinBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class CountryController extends Controller {

	/**
	 * List all the countries
	 * @return \Symfony\Component\HttpFoundation\Response 
	 */
	public function listAction(Request $request) {
		$countries = $this->getDoctrine()->getRepository('EuroCoinBundle:Country')->findAll();
		$translator = $this->get('translator');

		// Sort the countries by translated name
		$collator = new \Collator($request->getLocale());
		usort($countries, function ($a, $b) use ($collator, $translator) {
					$a_date = $a->getJoinDate();
					$b_date = $b->getJoinDate();

					if ($a_date != $b_date) {
						return ($a_date < $b_date) ? -1 : 1;
					}

					return $collator->compare(
						$translator->trans((string) $a),
						$translator->trans((string) $b)
					);
				});

		$countries_js = array();
		foreach ($countries as $country) {
			$countries_js[] = array(
				'name' => $translator->trans((string) $country),
				'nameiso' => $country->getNameIso(),
			);
		}

		return $this->render('EuroCoinBundle:Country:list.html.twig', array(
					'countries' => $countries,
					'countries_js' => json_encode($countries_js),
				));
	}

}
