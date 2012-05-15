<?php

namespace Euro\CoinBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Euro\CoinBundle\Entity\Share
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Euro\CoinBundle\Entity\ShareRepository")
 */
class Share {
	const STATUS_PENDING = 1;
	const STATUS_VALIDATED = 2;
	const STATUS_CANCELED = 3;

	/**
	 * @var integer $id
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @ORM\ManyToOne(targetEntity="UserCoin")
	 * @ORM\JoinColumn(name="from_user_coin_id", referencedColumnName="id")
	 */
	private $from_user_coin;

	/**
	 * @ORM\ManyToOne(targetEntity="UserCoin")
	 * @ORM\JoinColumn(name="to_user_coin_id", referencedColumnName="id")
	 */
	private $to_user_coin;

	/**
	 * @var smallint $status
	 *
	 * @ORM\Column(name="status", type="smallint")
	 */
	private $status;

	/**
	 * @var datetime $date
	 *
	 * @ORM\Column(name="date", type="datetime")
	 */
	private $date;

	public function __construct() {
		$this->status = self::STATUS_PENDING;
		$this->date = new \DateTime();
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
	 * Set from_user_coin
	 *
	 * @param UserCoin $fromUserCoin
	 */
	public function setFromUserCoin(UserCoin $fromUserCoin) {
		$this->from_user_coin = $fromUserCoin;

		return $this;
	}

	/**
	 * Get from_user_coin
	 *
	 * @return UserCoin
	 */
	public function getFromUserCoin() {
		return $this->from_user_coin;
	}

	/**
	 * Set to_user_coin
	 *
	 * @param UserCoin $toUserCoin
	 */
	public function setToUserCoin(UserCoin $toUserCoin) {
		$this->to_user_coin = $toUserCoin;

		return $this;
	}

	/**
	 * Get to_user_coin
	 *
	 * @return UserCoin
	 */
	public function getToUserCoin() {
		return $this->to_user_coin;
	}

	/**
	 * Set status
	 *
	 * @param smallint $status
	 */
	public function setStatus($status) {
		$this->status = $status;

		return $this;
	}

	/**
	 * Get status
	 *
	 * @return smallint
	 */
	public function getStatus() {
		return $this->status;
	}

	/**
	 * Set date
	 *
	 * @param \DateTime $date
	 */
	public function setDate(\DateTime $date) {
		$this->date = $date;

		return $this;
	}

	/**
	 * Get date
	 *
	 * @return \DateTime
	 */
	public function getDate() {
		return $this->date;
	}

}