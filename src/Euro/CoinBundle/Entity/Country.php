<?php

namespace Euro\CoinBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Euro\CoinBundle\Entity\Country
 *
 * @ORM\Table(name="country")
 * @ORM\Entity(repositoryClass="Euro\CoinBundle\Entity\CountryRepository")
 */
class Country {
	/**
	 * @var integer $id
	 *
	 * @ORM\Column(name="id", type="smallint")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @var string $name
	 *
	 * @ORM\Column(name="name", type="string", length=100)
	 */
	private $name;

	/**
	 * @var string $nameiso
	 *
	 * @ORM\Column(name="nameiso", type="string", length=2)
	 */
	private $nameiso;

	/**
	 * @var date $join_date
	 *
	 * @ORM\Column(name="join_date", type="date")
	 */
	private $join_date;

	/**
	 * @var string $former_currency_iso
	 *
	 * @ORM\Column(name="former_currency_iso", type="string", length=3)
	 */
	private $former_currency_iso;

	/**
	 * @var float $exchange_rate
	 *
	 * @ORM\Column(name="exchange_rate", type="float")
	 */
	private $exchange_rate;

	/**
	 * @ORM\OneToMany(targetEntity="Coin", mappedBy="country")
	 */
	protected $coins;

	public function __construct() {
		$this->coins = new \Doctrine\Common\Collections\ArrayCollection();
	}

	public function __toString() {
		return $this->getName();
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
	 * Set name
	 *
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * Get name
	 *
	 * @return string
	 */
	public function getName($prefix = true) {
		if ($this->name) {
			if ($prefix) {
				return 'country.name.' . $this->name;
			}

			return $this->name;
		}

		return null;
	}

	/**
	 * Set nameiso
	 *
	 * @param string $nameiso
	 */
	public function setNameiso($nameiso) {
		$this->nameiso = $nameiso;
	}

	/**
	 * Get nameiso
	 *
	 * @return string
	 */
	public function getNameiso() {
		return $this->nameiso;
	}

	/**
	 * Set join_date
	 *
	 * @param date $joinDate
	 */
	public function setJoinDate($joinDate) {
		$this->join_date = $joinDate;
	}

	/**
	 * Get join_date
	 *
	 * @return date
	 */
	public function getJoinDate() {
		return $this->join_date;
	}

	/**
	 * Get former_currency
	 *
	 * @return string
	 */
	public function getFormerCurrency() {
		if ($this->name) {
			return 'country.currency.' . $this->name;
		}

		return null;
	}

	/**
	 * Set former_currency_iso
	 *
	 * @param string $formerCurrencyIso
	 */
	public function setFormerCurrencyIso($formerCurrencyIso) {
		$this->former_currency_iso = $formerCurrencyIso;
	}

	/**
	 * Get former_currency_iso
	 *
	 * @return string
	 */
	public function getFormerCurrencyIso() {
		return $this->former_currency_iso;
	}

	/**
	 * Set exchange_rate
	 *
	 * @param float $exchangeRate
	 */
	public function setExchangeRate($exchangeRate) {
		$this->exchange_rate = $exchangeRate;
	}

	/**
	 * Get exchange_rate
	 *
	 * @return float
	 */
	public function getExchangeRate() {
		return $this->exchange_rate;
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