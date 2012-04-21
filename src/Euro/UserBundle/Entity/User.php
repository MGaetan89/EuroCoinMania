<?php

namespace Euro\UserBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * Euro\UserBundle\Entity\User
 *
 * @ORM\Table(name="members")
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
	 * @ORM\OneToMany(targetEntity="Euro\CoinBundle\Entity\UserCoin", mappedBy="user")
	 */
	protected $coins;

	public function __construct() {
		$this->coins = new \Doctrine\Common\Collections\ArrayCollection();
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
	 * Add coins
	 *
	 * @param \Euro\CoinBundle\Entity\Coin $coins
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
