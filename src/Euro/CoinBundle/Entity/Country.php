<?php

namespace Euro\CoinBundle\Entity;

use Application\Sonata\MediaBundle\Entity\Media;
use Doctrine\ORM\Mapping as ORM;

/**
 * Euro\CoinBundle\Entity\Country
 *
 * @ORM\Table(name="euro_coin__country")
 * @ORM\Entity(repositoryClass="Euro\CoinBundle\Entity\CountryRepository")
 */
class Country {
	/**
	 * @var integer $id
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @var string $name
	 *
	 * @ORM\Column(name="name", type="string", length=25)
	 */
	private $name;

	/**
	 * @var string $name_iso
	 *
	 * @ORM\Column(name="name_iso", type="string", length=2)
	 */
	private $name_iso;

	/**
	 * @var \DateTime $join_date
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
	 * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media")
	 * @ORM\JoinColumn(name="flag_id", referencedColumnName="id")
	 */
	private $flag;

	public function __toString() {
		return 'country.name.' . $this->getName();
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
	 * @return Country
	 */
	public function setName($name) {
		$this->name = $name;

		return $this;
	}

	/**
	 * Get name
	 *
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * Set name_iso
	 *
	 * @param string $nameIso
	 * @return Country
	 */
	public function setNameIso($nameIso) {
		$this->name_iso = $nameIso;

		return $this;
	}

	/**
	 * Get name_iso
	 *
	 * @return string
	 */
	public function getNameIso() {
		return $this->name_iso;
	}

	/**
	 * Set join_date
	 *
	 * @param \DateTime $joinDate
	 * @return Country
	 */
	public function setJoinDate($joinDate) {
		$this->join_date = $joinDate;

		return $this;
	}

	/**
	 * Get join_date
	 *
	 * @return \DateTime
	 */
	public function getJoinDate() {
		return $this->join_date;
	}

	/**
	 * Set former_currency_iso
	 *
	 * @param string $formerCurrencyIso
	 * @return Country
	 */
	public function setFormerCurrencyIso($formerCurrencyIso) {
		$this->former_currency_iso = $formerCurrencyIso;

		return $this;
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
	 * @return Country
	 */
	public function setExchangeRate($exchangeRate) {
		$this->exchange_rate = $exchangeRate;

		return $this;
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
	 * Set flag
	 *
	 * @param Media $flag
	 */
	public function setFlag(Media $flag) {
		$this->flag = $flag;

		return $this;
	}

	/**
	 * Get flag
	 *
	 * @return Media
	 */
	public function getFlag() {
		return $this->flag;
	}

}
