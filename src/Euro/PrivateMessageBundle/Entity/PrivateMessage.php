<?php

namespace Euro\PrivateMessageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Euro\UserBundle\Entity\User;

/**
 * Euro\PrivateMessageBundle\Entity\PrivateMessage
 *
 * @ORM\Table(name="private_message")
 * @ORM\Entity(repositoryClass="Euro\PrivateMessageBundle\Entity\PrivateMessageRepository")
 */
class PrivateMessage {
	/**
	 * @var integer $id
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @var string $conversation
	 *
	 * @ORM\Column(name="conversation", type="string", length=16)
	 */
	private $conversation;

	/**
	 * @ORM\ManyToOne(targetEntity="Euro\UserBundle\Entity\User")
	 * @ORM\JoinColumn(name="from_user_id", referencedColumnName="id")
	 */
	private $from_user;

	/**
	 * @ORM\ManyToOne(targetEntity="Euro\UserBundle\Entity\User")
	 * @ORM\JoinColumn(name="to_user_id", referencedColumnName="id")
	 */
	private $to_user;

	/**
	 * @var datetime $post_date
	 *
	 * @ORM\Column(name="post_date", type="datetime")
	 */
	private $post_date;

	/**
	 * @var string $title
	 *
	 * @ORM\Column(name="title", type="string", length=255, nullable=true)
	 */
	private $title;

	/**
	 * @var text $text
	 *
	 * @ORM\Column(name="text", type="text")
	 */
	private $text;

	/**
	 * @var boolean $is_read
	 *
	 * @ORM\Column(name="is_read", type="boolean")
	 */
	private $is_read;

	/**
	 * @var boolean $is_open
	 *
	 * @ORM\Column(name="is_open", type="boolean")
	 */
	private $is_open;

	public function __construct() {
		$this->conversation = uniqid();
		$this->post_date = new \DateTime();
		$this->is_read = false;
		$this->is_open = true;
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
	 * Set conversation
	 *
	 * @param string $conversation
	 */
	public function setConversation($conversation) {
		$this->conversation = $conversation;
	}

	/**
	 * Get conversation
	 *
	 * @return string
	 */
	public function getConversation() {
		return $this->conversation;
	}

	/**
	 * Set from_user
	 *
	 * @param User $from_user
	 */
	public function setFromUser(User $from_user) {
		$this->from_user = $from_user;
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
	 */
	public function setToUser(User $to_user) {
		$this->to_user = $to_user;
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
	 * Set post_date
	 *
	 * @param datetime $postDate
	 */
	public function setPostDate($postDate) {
		$this->post_date = $postDate;
	}

	/**
	 * Get post_date
	 *
	 * @return datetime
	 */
	public function getPostDate() {
		return $this->post_date;
	}

	/**
	 * Set title
	 *
	 * @param string $title
	 */
	public function setTitle($title) {
		$this->title = $title;
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
	 * Set text
	 *
	 * @param text $text
	 */
	public function setText($text) {
		$this->text = $text;
	}

	/**
	 * Get text
	 *
	 * @return text
	 */
	public function getText() {
		return $this->text;
	}

	/**
	 * Set is_read
	 *
	 * @param boolean $isRead
	 */
	public function setIsRead($isRead) {
		$this->is_read = $isRead;
	}

	/**
	 * Get is_read
	 *
	 * @return boolean
	 */
	public function getIsRead() {
		return $this->is_read;
	}

	/**
	 * Set is_open
	 *
	 * @param boolean $isOpen
	 */
	public function setIsOpen($isOpen) {
		$this->is_open = $isOpen;
	}

	/**
	 * Get is_open
	 *
	 * @return boolean
	 */
	public function getIsOpen() {
		return $this->is_open;
	}

}
