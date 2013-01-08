<?php

namespace Euro\CoinBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Euro\CoinBundle\Entity\Year
 *
 * @ORM\Table(name="euro_coin__year")
 * @ORM\Entity(repositoryClass="Euro\CoinBundle\Entity\YearRepository")
 */
class Year {
	/**
	 * @var integer $id
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @var smallint $year
	 *
	 * @ORM\Column(name="year", type="smallint")
	 */
	private $year;

	/**
	 * @ORM\ManyToOne(targetEntity="Workshop")
	 * @ORM\JoinColumn(name="workshop_id", referencedColumnName="id")
	 */
	private $workshop;

	public function __toString() {
		if ($this->getWorkshop()) {
			return $this->getYear() . ' ' . $this->getWorkshop();
		}

		return (string) $this->getYear();
	}

	/**
	 * Get id
	 *
	 * @return integer
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Set year
	 *
	 * @param integer $year
	 * @return Year
	 */
	public function setYear($year) {
		$this->year = $year;

		return $this;
	}

	/**
	 * Get year
	 *
	 * @return integer
	 */
	public function getYear() {
		return $this->year;
	}

	/**
	 * Set workshop
	 *
	 * @param Workshop $workshop
	 */
	public function setWorkshop(Workshop $workshop) {
		$this->workshop = $workshop;

		return $this;
	}

	/**
	 * Get workshop
	 *
	 * @return Workshop
	 */
	public function getWorkshop() {
		return $this->workshop;
	}

}
