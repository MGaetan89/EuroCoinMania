<?php

namespace Euro\PrivateMessageBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Euro\PrivateMessageBundle\Entity\Message
 *
 * @ORM\Table(name="euro_pm__message")
 * @ORM\Entity(repositoryClass="Euro\PrivateMessageBundle\Entity\MessageRepository")
 */
class Message {

	const DIRECTION_FROM_TO = 0;
	const DIRECTION_TO_FROM = 1;

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
	 * @var string $content
	 *
	 * @ORM\Column(name="content", type="text")
	 */
	private $content;

	/**
	 * @var boolean $direction
	 *
	 * @ORM\Column(name="direction", type="boolean")
	 */
	private $direction;

	/**
	 * @var boolean $new
	 *
	 * @ORM\Column(name="new", type="boolean")
	 */
	private $new;

	/**
	 * @var Conversation $conversation
	 *
	 * @ORM\ManyToOne(targetEntity="Conversation")
	 * @ORM\JoinColumn(name="conversation_id", referencedColumnName="id")
	 */
	private $conversation;

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
	 * @return Message
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
	 * Set content
	 *
	 * @param string $content
	 * @return Message
	 */
	public function setContent($content) {
		$this->content = $content;

		return $this;
	}

	/**
	 * Get content
	 *
	 * @return string 
	 */
	public function getContent() {
		return $this->content;
	}

	/**
	 * Set direction
	 *
	 * @param boolean $direction
	 * @return Message
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
	 * Set new
	 *
	 * @param boolean $new
	 * @return Message
	 */
	public function setNew($new) {
		$this->new = $new;

		return $this;
	}

	/**
	 * Get new
	 *
	 * @return boolean 
	 */
	public function isNew() {
		return $this->new;
	}

	/**
	 * Set conversation
	 *
	 * @param Conversation $conversation
	 * @return Message
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