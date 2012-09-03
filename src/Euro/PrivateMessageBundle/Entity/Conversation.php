<?php

namespace Euro\PrivateMessageBundle\Entity;

use Application\Sonata\UserBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Euro\CoinBundle\Entity\Share;

/**
 * Euro\PrivateMessageBundle\Entity\Conversation
 *
 * @ORM\Table(name="euro_pm__conversation")
 * @ORM\Entity(repositoryClass="Euro\PrivateMessageBundle\Entity\ConversationRepository")
 */
class Conversation {

	/**
	 * @var integer $id
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @var User $from_user
	 *
	 * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User")
	 * @ORM\JoinColumn(name="from_user_id", referencedColumnName="id")
	 */
	private $from_user;

	/**
	 * @var User $to_user
	 *
	 * @ORM\ManyToOne(targetEntity="Application\Sonata\UserBundle\Entity\User")
	 * @ORM\JoinColumn(name="to_user_id", referencedColumnName="id")
	 */
	private $to_user;

	/**
	 * @var string $title
	 *
	 * @ORM\Column(name="title", type="string", length=255)
	 */
	private $title;

	/**
	 * @var boolean $open
	 *
	 * @ORM\Column(name="open", type="boolean")
	 */
	private $open;

	/**
	 * @var Share $share
	 *
	 * @ORM\ManyToOne(targetEntity="Euro\CoinBundle\Entity\Share")
	 * @ORM\JoinColumn(name="share_id", referencedColumnName="id")
	 */
	private $share;

	/**
	 * @var ArrayCollection $messages
	 *
	 * @ORM\OneToMany(targetEntity="Message", mappedBy="conversation")
	 */
	protected $messages;

	public function __construct() {
		$this->messages = new ArrayCollection();
		$this->open = true;
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
	 * Set from_user
	 *
	 * @param User $from_user
	 * @return Conversation
	 */
	public function setFromUser(User $from_user) {
		$this->from_user = $from_user;

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
	 * @param User $to_user
	 * @return Conversation
	 */
	public function setToUser(User $to_user) {
		$this->to_user = $to_user;

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
	 * Set title
	 *
	 * @param string $title
	 * @return Conversation
	 */
	public function setTitle($title) {
		$this->title = $title;

		return $this;
	}

	/**
	 * Get title
	 *
	 * @return string 
	 */
	public function getTitle() {
		return $this->title;
	}

	/**
	 * Set open
	 *
	 * @param boolean $open
	 * @return Conversation
	 */
	public function setOpen($open) {
		$this->open = $open;

		return $this;
	}

	/**
	 * Get open
	 *
	 * @return boolean 
	 */
	public function isOpen() {
		return $this->open;
	}

	/**
	 * Set share
	 *
	 * @param Share $share
	 * @return Conversation
	 */
	public function setShare(Share $share) {
		$this->share = $share;

		return $this;
	}

	/**
	 * Get share
	 *
	 * @return Share
	 */
	public function getShare() {
		return $this->share;
	}

	/**
	 * Add messages
	 *
	 * @param Message $messages
	 * @return Conversation
	 */
	public function addMessage(Message $messages) {
		$this->messages[] = $messages;

		return $this;
	}

	/**
	 * Remove messages
	 *
	 * @param Message $messages
	 */
	public function removeMessage(Message $messages) {
		$this->messages->removeElement($messages);
	}

	/**
	 * Get messages
	 *
	 * @return ArrayCollection 
	 */
	public function getMessages() {
		return $this->messages;
	}

}