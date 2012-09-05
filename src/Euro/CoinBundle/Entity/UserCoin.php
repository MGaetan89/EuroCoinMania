<?php

namespace Euro\CoinBundle\Entity;

use Application\Sonata\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * Euro\CoinBundle\Entity\UserCoin
 *
 * @ORM\Table(name="euro_coin__usercoin")
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
	 * @var User $user
	 *
	 * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User")
	 * @ORM\JoinColumn(name="user_id", referencedColumnName="id")
	 */
	private $user;

	/**
	 * @ORM\ManyToOne(targetEntity="Coin")
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
	 * @var integer $sharing
	 *
	 * @ORM\Column(name="sharing", type="integer")
	 */
	private $sharing;

	public function __construct() {
		$this->quantity = 1;
		$this->sharing = 0;
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
	 * Set user
	 *
	 * @param User $user
	 * @return UserCoin
	 */
	public function setUser(User $user) {
		$this->user = $user;

		return $this;
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
	 * @return UserCoin
	 */
	public function setCoin(Coin $coin) {
		$this->coin = $coin;

		return $this;
	}

	/**
	 * Get coin
	 *
	 * @return Coin
	 */
	public function getCoin() {
		return $this->coin;
	}

	/**
	 * Add one coin
	 *
	 * @return UserCoin
	 */
	public function addUnit() {
		++$this->quantity;

		return $this;
	}

	/**
	 * Remove one coin
	 *
	 * @return UserCoin
	 */
	public function removeUnit() {
		--$this->quantity;

		return $this;
	}

	/**
	 * Set quantity
	 *
	 * @param integer $quantity
	 * @return UserCoin
	 */
	public function setQuantity($quantity) {
		$this->quantity = $quantity;

		return $this;
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
	 * Add one exchange
	 *
	 * @return UserCoin
	 */
	public function addExchange() {
		++$this->sharing;

		return $this;
	}

	/**
	 * Remove one exchange
	 *
	 * @return UserCoin
	 */
	public function removeExchange() {
		--$this->sharing;

		return $this;
	}

	/**
	 * Set sharing
	 *
	 * @param integer $sharing
	 * @return UserCoin
	 */
	public function setSharing($sharing) {
		$this->sharing = $sharing;

		return $this;
	}

	/**
	 * Get sharing
	 *
	 * @return integer 
	 */
	public function getSharing() {
		return $this->sharing;
	}

}
