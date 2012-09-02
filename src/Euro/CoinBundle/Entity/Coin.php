<?php

namespace Euro\CoinBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Euro\CoinBundle\Entity\Coin
 *
 * @ORM\Table(name="euro_coin__coin")
 * @ORM\Entity(repositoryClass="Euro\CoinBundle\Entity\CoinRepository")
 */
class Coin {

	/**
	 * @var integer $id
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @ORM\ManyToOne(targetEntity="Country")
	 * @ORM\JoinColumn(name="country_id", referencedColumnName="id")
	 */
	private $country;

	/**
	 * @ORM\ManyToOne(targetEntity="Value")
	 * @ORM\JoinColumn(name="value_id", referencedColumnName="id")
	 */
	private $value;

	/**
	 * @ORM\ManyToOne(targetEntity="Year")
	 * @ORM\JoinColumn(name="year_id", referencedColumnName="id")
	 */
	private $year;

	/**
	 * @var boolean $collector
	 *
	 * @ORM\Column(name="collector", type="boolean")
	 */
	private $collector;

	/**
	 * @var string $description
	 *
	 * @ORM\Column(name="description", type="text", nullable=true)
	 */
	private $description;

	/**
	 * @var integer $mintage
	 *
	 * @ORM\Column(name="mintage", type="integer")
	 */
	private $mintage;

	/**
	 * @var integer $member_total
	 *
	 * @ORM\Column(name="member_total", type="integer")
	 */
	private $member_total;

	/**
	 * Get id
	 *
	 * @return integer 
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Set collector
	 *
	 * @param boolean $collector
	 * @return Coin
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

	/**
	 * Set description
	 *
	 * @param string $description
	 * @return Coin
	 */
	public function setDescription($description) {
		$this->description = $description;

		return $this;
	}

	/**
	 * Get description
	 *
	 * @return string 
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * Set mintage
	 *
	 * @param integer $mintage
	 * @return Coin
	 */
	public function setMintage($mintage) {
		$this->mintage = $mintage;

		return $this;
	}

	/**
	 * Get mintage
	 *
	 * @return integer 
	 */
	public function getMintage() {
		return $this->mintage;
	}

	/**
	 * Add one coin
	 *
	 * @return Coin
	 */
	public function addUnit() {
		$this->member_total++;

		return $this;
	}

	/**
	 * Remove one coin
	 *
	 * @return Coin
	 */
	public function removeUnit() {
		$this->member_total--;

		return $this;
	}

	/**
	 * Set member_total
	 *
	 * @param integer $memberTotal
	 * @return Coin
	 */
	public function setMemberTotal($memberTotal) {
		$this->member_total = $memberTotal;

		return $this;
	}

	/**
	 * Get member_total
	 *
	 * @return integer 
	 */
	public function getMemberTotal() {
		return $this->member_total;
	}

	/**
	 * Set country
	 *
	 * @param Country $country
	 */
	public function setCountry(Country $country) {
		$this->country = $country;

		return $this;
	}

	/**
	 * Get country
	 *
	 * @return Country
	 */
	public function getCountry() {
		return $this->country;
	}

	/**
	 * Set value
	 *
	 * @param Value $value
	 */
	public function setValue(Value $value) {
		$this->value = $value;

		return $this;
	}

	/**
	 * Get value
	 *
	 * @return Value
	 */
	public function getValue() {
		return $this->value;
	}

	/**
	 * Set year
	 *
	 * @param Year $year
	 */
	public function setYear(Year $year) {
		$this->year = $year;

		return $this;
	}

	/**
	 * Get year
	 *
	 * @return Year
	 */
	public function getYear() {
		return $this->year;
	}

}
