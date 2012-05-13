<?php

namespace Euro\UserBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Euro\CoinBundle\Entity\Coin;
use FOS\UserBundle\Entity\User as BaseUser;

/**
 * Euro\UserBundle\Entity\User
 *
 * @ORM\Table(name="member")
 * @ORM\Entity(repositoryClass="Euro\UserBundle\Entity\UserRepository")
 */
class User extends BaseUser {
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

	/**
	 * @var datetime $registration_date
	 *
	 * @ORM\Column(name="registration_date", type="datetime")
	 */
	protected $registration_date;

	/**
	 * @ORM\OneToMany(targetEntity="Euro\CoinBundle\Entity\UserCoin", mappedBy="user")
	 */
	protected $coins;

	public function __construct() {
		parent::__construct();

		$this->coins = new ArrayCollection();
		$this->registration_date = new \DateTime();
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
	 * Set registration_date
	 *
	 * @param \DateTime $registration_date
	 */
	public function setRegistrationDate(\DateTime $registration_date) {
		$this->registration_date = $registration_date;

		return $this;
	}

	/**
	 * Get registration_date
	 *
	 * @return \DateTime
	 */
	public function getRegistrationDate() {
		return $this->registration_date;
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
