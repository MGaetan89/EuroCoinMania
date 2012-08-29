<?php

namespace Euro\CoinBundle\Entity;

use Application\Sonata\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;

/**
 * Euro\CoinBundle\Entity\Share
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Euro\CoinBundle\Entity\ShareRepository")
 */
class Share {

	const STATUS_PENDING = 1;
	const STATUS_ACCEPTED = 2;
	const STATUS_CANCELED = 3;
	const STATUS_REFUSED = 4;

	/**
	 * @var integer $id
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @var \DateTime $date
	 *
	 * @ORM\Column(name="date", type="datetime")
	 */
	private $date;

	/**
	 * @var integer $status
	 *
	 * @ORM\Column(name="status", type="smallint")
	 */
	private $status;

	/**
	 * @var integer $from_user
	 *
	 * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User")
	 * @ORM\JoinColumn(name="from_user_id", referencedColumnName="id")
	 */
	private $from_user;

	/**
	 * @var integer $to_user
	 *
	 * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User")
	 * @ORM\JoinColumn(name="to_user_id", referencedColumnName="id")
	 */
	private $to_user;

	/**
	 * @var array $coins_requested
	 *
	 * @ORM\Column(name="coins_requested", type="array")
	 */
	private $coins_requested;

	/**
	 * @var array $coins_suggested
	 *
	 * @ORM\Column(name="coins_suggested", type="array")
	 */
	private $coins_suggested;

	public function __construct() {
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
	 * Set date
	 *
	 * @param \DateTime $date
	 * @return Share
	 */
	public function setDate($date) {
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

	/**
	 * Set status
	 *
	 * @param integer $status
	 * @return Share
	 */
	public function setStatus($status) {
		$this->status = $status;

		return $this;
	}

	/**
	 * Get status
	 *
	 * @return integer 
	 */
	public function getStatus() {
		return $this->status;
	}

	/**
	 * Set from_user
	 *
	 * @param User $fromUser
	 * @return Share
	 */
	public function setFromUser(User $fromUser) {
		$this->from_user = $fromUser;

		return $this;
	}

	/**
	 * Get from_user
	 *
	 * @return User 
	 */
	public function getFromUser() {
		return $this->from_user;
	}

	/**
	 * Set to_user
	 *
	 * @param User $toUser
	 * @return Share
	 */
	public function setToUser(User $toUser) {
		$this->to_user = $toUser;

		return $this;
	}

	/**
	 * Get to_user
	 *
	 * @return User 
	 */
	public function getToUser() {
		return $this->to_user;
	}

	/**
	 * Set coins_requested
	 *
	 * @param array $coinsRequested
	 * @return Share
	 */
	public function setCoinsRequested(array $coinsRequested) {
		$this->coins_requested = $coinsRequested;

		return $this;
	}

	/**
	 * Get coins_requested
	 *
	 * @return array 
	 */
	public function getCoinsRequested() {
		return $this->coins_requested;
	}

	/**
	 * Set coins_suggested
	 *
	 * @param array $coinsSuggested
	 * @return Share
	 */
	public function setCoinsSuggested(array $coinsSuggested) {
		$this->coins_suggested = $coinsSuggested;

		return $this;
	}

	/**
	 * Get coins_suggested
	 *
	 * @return array 
	 */
	public function getCoinsSuggested() {
		return $this->coins_suggested;
	}

}
