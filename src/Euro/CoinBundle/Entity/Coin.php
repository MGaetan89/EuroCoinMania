<?php

namespace Euro\CoinBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Euro\CoinBundle\Entity\Country;
use Euro\CoinBundle\Entity\Value;
use Euro\UserBundle\Entity\User;

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
	 * @ORM\Column(name="id", type="smallint")
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
	 * @ORM\ManyToOne(targetEntity="Country", inversedBy="coins")
	 * @ORM\JoinColumn(name="country_id", referencedColumnName="id")
	 */
	private $country;

	/**
	 * @var string $year
	 *
	 * @ORM\Column(name="year", type="string", length=10)
	 */
	private $year;

	/**
	 * @var boolean $commemorative
	 *
	 * @ORM\Column(name="commemorative", nullable=true, type="boolean")
	 */
	private $commemorative;

	/**
	 * @var bigint $mintage
	 *
	 * @ORM\Column(name="mintage", type="integer")
	 */
	private $mintage;

	/**
	 * @var text $description
	 *
	 * @ORM\Column(name="description", nullable=true, type="string")
	 */
	private $description;

	/**
	 * @ORM\OneToMany(targetEntity="UserCoin", mappedBy="coin")
	 */
	private $users;

	public function __construct() {
		$this->users = new ArrayCollection();
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
	 * @param string $year
	 */
	public function setYear($year) {
		$this->year = $year;

		return $this;
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
	 * Set commemorative
	 *
	 * @param boolean $commemorative
	 */
	public function setCommemorative($commemorative) {
		$this->commemorative = $commemorative;

		return $this;
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

		return $this;
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

		return $this;
	}

	/**
	 * Get description
	 *
	 * @return text
	 */
	public function getDescription() {
		return $this->description;
	}

	/**
	 * Add users
	 *
	 * @param User $users
	 */
	public function addUser(User $users) {
		$this->users[] = $users;

		return $this;
	}

	/**
	 * Get users
	 *
	 * @return Collection
	 */
	public function getUsers() {
		return $this->users;
	}

}
