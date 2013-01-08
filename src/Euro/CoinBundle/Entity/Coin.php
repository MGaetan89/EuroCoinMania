<?php

namespace Euro\CoinBundle\Entity;

use Application\Sonata\MediaBundle\Entity\Media;
use Doctrine\ORM\Mapping as ORM;

/**
 * Euro\CoinBundle\Entity\Coin
 *
 * @ORM\Table(name="euro_coin__coin")
 * @ORM\Entity(repositoryClass="Euro\CoinBundle\Entity\CoinRepository")
 */
class Coin {
	const TYPE_CIRCULATION = 1;
	const TYPE_COMMEMORATIVE = 2;
	const TYPE_COLLECTOR = 3;

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
	 * @var integer $type
	 *
	 * @ORM\Column(name="type", type="smallint")
	 */
	private $type;

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
	 * @ORM\ManyToOne(targetEntity="Application\Sonata\MediaBundle\Entity\Media")
	 * @ORM\JoinColumn(name="image_id", referencedColumnName="id")
	 */
	private $image;

	/**
	 * Get id
	 *
	 * @return integer
	 */
	public function getId() {
		return $this->id;
	}

	/**
	 * Set type
	 *
	 * @param integer $type
	 * @return Coin
	 */
	public function setType($type) {
		$this->type = $type;

		return $this;
	}

	/**
	 * Get type
	 *
	 * @return integer
	 */
	public function getType() {
		return $this->type;
	}

	/**
	 * Get types
	 *
	 * @return array
	 */
	public static function getTypes() {
		return array(
			self::TYPE_CIRCULATION => 'coin.type1',
			self::TYPE_COMMEMORATIVE => 'coin.type2',
			self::TYPE_COLLECTOR => 'coin.type3',
		);
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
	public function addUnit($quantity = 1) {
		$this->member_total += $quantity;

		return $this;
	}

	/**
	 * Remove one coin
	 *
	 * @return Coin
	 */
	public function removeUnit($quantity = 1) {
		$this->member_total -= $quantity;

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

	/**
	 * Set image
	 *
	 * @param Media $image
	 */
	public function setImage(Media $image) {
		$this->image = $image;

		return $this;
	}

	/**
	 * Get image
	 *
	 * @return Media
	 */
	public function getImage() {
		return $this->image;
	}

}
