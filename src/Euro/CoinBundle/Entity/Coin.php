<?php

namespace Euro\CoinBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Euro\CoinBundle\Entity\Coin
 *
 * @ORM\Table(name="coin")
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
	 * @ORM\ManyToOne(targetEntity="Value", inversedBy="coins")
	 * @ORM\JoinColumn(name="value_id", referencedColumnName="id")
	 */
	private $value;

	/**
	 * @var string $year
	 *
	 * @ORM\Column(name="year", type="string", length=5)
	 */
	private $year;

	/**
	 * @ORM\ManyToOne(targetEntity="Country", inversedBy="coins")
	 * @ORM\JoinColumn(name="country_id", referencedColumnName="id")
	 */
	private $country;

	/**
	 * @var boolean $commemorative
	 *
	 * @ORM\Column(name="commemorative", nullable=true, type="boolean")
	 */
	private $commemorative;

	/**
	 * @var bigint $mintage
	 *
	 * @ORM\Column(name="mintage", type="bigint")
	 */
	private $mintage;

	/**
	 * @var text $description
	 *
	 * @ORM\Column(name="description", nullable=true, type="text")
	 */
	private $description;

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
	 * @param Euro\CoinBundle\Entity\Value $value
	 */
	public function setValue(\Euro\CoinBundle\Entity\Value $value) {
		$this->value = $value;
	}

	/**
	 * Get value
	 *
	 * @return Euro\CoinBundle\Entity\Value
	 */
	public function getValue() {
		return $this->value;
	}

	/**
	 * Set year
	 *
	 * @param string $year
	 */
	public function setYear($year) {
		$this->year = $year;
	}

	/**
	 * Get year
	 *
	 * @return string
	 */
	public function getYear() {
		return $this->year;
	}

	/**
	 * Set country
	 *
	 * @param Euro\CoinBundle\Entity\Country $country
	 */
	public function setCountry(\Euro\CoinBundle\Entity\Country $country) {
		$this->country = $country;
	}

	/**
	 * Get country
	 *
	 * @return Euro\CoinBundle\Entity\Country
	 */
	public function getCountry() {
		return $this->country;
	}

	/**
	 * Set commemorative
	 *
	 * @param boolean $commemorative
	 */
	public function setCommemorative($commemorative) {
		$this->commemorative = $commemorative;
	}

	/**
	 * Get commemorative
	 *
	 * @return boolean
	 */
	public function getCommemorative() {
		return $this->commemorative;
	}

	/**
	 * Set mintage
	 *
	 * @param bigint $mintage
	 */
	public function setMintage($mintage) {
		$this->mintage = $mintage;
	}

	/**
	 * Get mintage
	 *
	 * @return bigint
	 */
	public function getMintage() {
		return $this->mintage;
	}

	/**
	 * Set description
	 *
	 * @param text $description
	 */
	public function setDescription($description) {
		$this->description = $description;
	}

	/**
	 * Get description
	 *
	 * @return text
	 */
	public function getDescription() {
		return $this->description;
	}

}