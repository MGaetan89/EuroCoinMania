<?php

namespace Euro\CoinBundle\Entity;

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
	 * @ORM\Column(name="id", type="integer")
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
	 * @ORM\OneToMany(targetEntity="Coin", mappedBy="country")
	 */
	protected $coins;

	public function __construct() {
		$this->coins = new \Doctrine\Common\Collections\ArrayCollection();
	}

	public function __toString() {
		return $this->value . ' €';
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
	 * @param Euro\CoinBundle\Entity\Coin $coins
	 */
	public function addCoin(\Euro\CoinBundle\Entity\Coin $coins) {
		$this->coins[] = $coins;
	}

	/**
	 * Get coins
	 *
	 * @return Doctrine\Common\Collections\Collection
	 */
	public function getCoins() {
		return $this->coins;
	}

}
