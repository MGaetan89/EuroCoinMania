<?php

namespace Euro\CoinBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Euro\CoinBundle\Entity\Coin;
use Euro\CoinBundle\Entity\Workshop;

/**
 * Euro\CoinBundle\Entity\Year
 *
 * @ORM\Table()
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

	/**
	 * @ORM\OneToMany(targetEntity="Coin", mappedBy="country")
	 */
	protected $coins;

	public function __construct() {
		$this->coins = new ArrayCollection();
	}

	public function __toString() {
		$year = (string) $this->year;
		if ($this->getWorkshop()) {
			$year .= $this->getWorkshop()->getShortName();
		}

		return $year;
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
	 * @param smallint $year
	 */
	public function setYear($year) {
		$this->year = $year;
	}

	/**
	 * Get year
	 *
	 * @return smallint
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
	}

	/**
	 * Get workshop
	 *
	 * @return Workshop
	 */
	public function getWorkshop() {
		return $this->workshop;
	}

	/**
	 * Add coins
	 *
	 * @param Coin $coins
	 */
	public function addCoin(Coin $coins) {
		$this->coins[] = $coins;

		return $this;
	}

	/**
	 * Get coins
	 *
	 * @return Collection
	 */
	public function getCoins() {
		return $this->coins;
	}

}
