<?php

namespace Euro\CoinBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Euro\CoinBundle\Entity\Value
 *
 * @ORM\Table(name="euro_coin__value")
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
	 * @ORM\Column(name="collector", type="boolean")
	 */
	private $collector;

	public function __toString() {
		return $this->getValue() . ' €';
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
	 * @return Value
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
	 * @return Value
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
	public function isCollector() {
		return $this->collector;
	}

}
