<?php

namespace Euro\CoinBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

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
	 * @param Euro\UserBundle\Entity\User $user
	 */
	public function setUser(\Euro\UserBundle\Entity\User $user) {
		$this->user = $user;
	}

	/**
	 * Get user
	 *
	 * @return Euro\UserBundle\Entity\User
	 */
	public function getUser() {
		return $this->user;
	}

	/**
	 * Set coin
	 *
	 * @param Euro\CoinBundle\Entity\Coin $coin
	 */
	public function setCoin(\Euro\CoinBundle\Entity\Coin $coin) {
		$this->coin = $coin;
	}

	/**
	 * Get coin
	 *
	 * @return Euro\CoinBundle\Entity\Coin
	 */
	public function getCoin() {
		return $this->coin;
	}

}
