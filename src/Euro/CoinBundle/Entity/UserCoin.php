<?php

namespace Euro\CoinBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Euro\CoinBundle\Entity\Coin;
use Euro\UserBundle\Entity\User;

/**
 * Euro\CoinBundle\Entity\UserCoin
 *
 * @ORM\Table(name="user_coin")
 * @ORM\Entity(repositoryClass="Euro\CoinBundle\Entity\UserCoinRepository")
 */
class UserCoin {
	/**
	 * @var integer $id
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @ORM\ManyToOne(targetEntity="Euro\UserBundle\Entity\User", inversedBy="coins")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 */
	private $user;

	/**
	 * @ORM\ManyToOne(targetEntity="Coin", inversedBy="users")
	 * @ORM\JoinColumn(name="coin_id", referencedColumnName="id")
	 */
	private $coin;

	/**
	 * @var integer $quantity
	 *
	 * @ORM\Column(name="quantity", type="integer")
	 */
	private $quantity;

	public function __construct() {
		$this->quantity = 1;
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
	 * Set quantity
	 *
	 * @param integer $quantity
	 */
	public function setQuantity($quantity) {
		$this->quantity = $quantity;
	}

	/**
	 * Get quantity
	 *
	 * @return integer
	 */
	public function getQuantity() {
		return $this->quantity;
	}

	/**
	 * Set user
	 *
	 * @param User $user
	 */
	public function setUser(User $user) {
		$this->user = $user;
	}

	/**
	 * Get user
	 *
	 * @return User
	 */
	public function getUser() {
		return $this->user;
	}

	/**
	 * Set coin
	 *
	 * @param Coin $coin
	 */
	public function setCoin(Coin $coin) {
		$this->coin = $coin;
	}

	/**
	 * Get coin
	 *
	 * @return Coin
	 */
	public function getCoin() {
		return $this->coin;
	}

}
