<?php

namespace Euro\PrivateMessageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Euro\PrivateMessageBundle\Entity\PrivateMessage
 *
 * @ORM\Table(name="private_message")
 * @ORM\Entity(repositoryClass="Euro\PrivateMessageBundle\Entity\PrivateMessageRepository")
 */
class PrivateMessage {
	const DIRECTION_FROM_TO = true;
	const DIRECTION_TO_FROM = false;

	/**
	 * @var integer $id
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @ORM\ManyToOne(targetEntity="Conversation")
	 * @ORM\JoinColumn(name="conversation_id", referencedColumnName="id")
	 */
	private $conversation;

	/**
	 * @var datetime $post_date
	 *
	 * @ORM\Column(name="post_date", type="datetime")
	 */
	private $post_date;

	/**
	 * @var text $text
	 *
	 * @ORM\Column(name="text", type="text")
	 */
	private $text;

	/**
	 * @var boolean $direction
	 *
	 * @ORM\Column(name="direction", type="boolean")
	 */
	private $direction;

	/**
	 * @var boolean $is_read
	 *
	 * @ORM\Column(name="is_read", type="boolean")
	 */
	private $is_read;

	public function __construct() {
		$this->is_read = false;
		$this->post_date = new \DateTime();
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
	 * @param Conversation $conversation
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

	/**
	 * Set post_date
	 *
	 * @param datetime $postDate
	 */
	public function setPostDate($postDate) {
		$this->post_date = $postDate;

		return $this;
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
	 * Set text
	 *
	 * @param text $text
	 */
	public function setText($text) {
		$this->text = $text;

		return $this;
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
	 * Set direction
	 *
	 * @param boolean $direction
	 */
	public function setDirection($direction) {
		$this->direction = $direction;

		return $this;
	}

	/**
	 * Get direction
	 *
	 * @return boolean
	 */
	public function getDirection() {
		return $this->direction;
	}

	/**
	 * Set is_read
	 *
	 * @param boolean $isRead
	 */
	public function setIsRead($isRead) {
		$this->is_read = $isRead;

		return $this;
	}

	/**
	 * Get is_read
	 *
	 * @return boolean
	 */
	public function getIsRead() {
		return $this->is_read;
	}

}
