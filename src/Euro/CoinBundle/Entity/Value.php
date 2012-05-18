<?php

namespace Euro\CoinBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Euro\CoinBundle\Entity\Value
 *
 * @ORM\Table(name="value")
 * @ORM\Entity(repositoryClass="Euro\CoinBundle\Entity\ValueRepository")
 */
class Value {
	/**
	 * @var integer $id
	 *
	 * @ORM\Column(name="id", type="smallint")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @var float $value
	 *
	 * @ORM\Column(name="value", type="float")
	 */
	private $value;

	/**
	 * @var boolean $collector
	 *
	 * @ORM\Column(name="collector", nullable=true, type="boolean")
	 */
	private $collector;

	/**
	 * @ORM\OneToMany(targetEntity="Coin", mappedBy="value")
	 */
	protected $coins;

	public function __construct() {
		$this->coins = new ArrayCollection();
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
	 * Set value
	 *
	 * @param float $value
	 */
	public function setValue($value) {
		$this->value = $value;

		return $this;
	}

	/**
	 * Get value
	 *
	 * @return float
	 */
	public function getValue() {
		return $this->value;
	}

	/**
	 * Set collector
	 *
	 * @param boolean $collector
	 */
	public function setCollector($collector) {
		$this->collector = $collector;

		return $this;
	}

	/**
	 * Get collector
	 *
	 * @return boolean
	 */
	public function getCollector() {
		return $this->collector;
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
