<?php

namespace Euro\CoinBundle\Entity;

use Application\Sonata\UserBundle\Entity\User;
use Doctrine\ORM\Mapping as ORM;
use Euro\PrivateMessageBundle\Entity\Conversation;

/**
 * Euro\CoinBundle\Entity\Exchange
 *
 * @ORM\Table(name="euro_coin__exchange")
 * @ORM\Entity(repositoryClass="Euro\CoinBundle\Entity\ExchangeRepository")
 */
class Exchange {

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

	/**
	 * @var integer $conversation
	 *
	 * @ORM\ManyToOne(targetEntity="Euro\PrivateMessageBundle\Entity\Conversation")
	 * @ORM\JoinColumn(name="conversation_id", referencedColumnName="id")
	 */
	private $conversation;

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
	 * @return Exchange
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
	 * @return Exchange
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
	 * @return Exchange
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
	 * @return Exchange
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
	 * @return Exchange
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
	 * @return Exchange
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

	/**
	 * Set conversation
	 *
	 * @param Conversation $conversation
	 * @return Exchange
	 */
	public function setConversation(Conversation $conversation) {
		$this->conversation = $conversation;

		return $this;
	}

	/**
	 * Get conversation
	 *
	 * @return Conversation 
	 */
	public function getConversation() {
		return $this->conversation;
	}

}
