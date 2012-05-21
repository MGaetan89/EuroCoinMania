<?php

namespace Euro\PrivateMessageBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Euro\CoinBundle\Entity\Share;
use Euro\UserBundle\Entity\User;

/**
 * Euro\PrivateMessageBundle\Entity\Conversation
 *
 * @ORM\Table(name="conversation")
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
	 * @var string $title
	 *
	 * @ORM\Column(name="title", type="string", length=255)
	 */
	private $title;

	/**
	 * @var boolean $is_open
	 *
	 * @ORM\Column(name="is_open", type="boolean")
	 */
	private $is_open;

	/**
	 * @ORM\OneToOne(targetEntity="Euro\CoinBundle\Entity\Share", inversedBy="pm")
	 * @ORM\JoinColumn(name="share_id", referencedColumnName="id")
	 */
	private $share;

	/**
	 * @ORM\OneToMany(targetEntity="PrivateMessage", mappedBy="conversation")
	 * @ORM\OrderBy({"post_date" = "DESC"})
	 */
	protected $pm;

	public function __construct() {
		$this->pm = new ArrayCollection();
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
	 * Set from_user
	 *
	 * @param User $from_user
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
	 * Set is_open
	 *
	 * @param boolean $isOpen
	 */
	public function setIsOpen($isOpen) {
		$this->is_open = $isOpen;

		return $this;
	}

	/**
	 * Get is_open
	 *
	 * @return boolean
	 */
	public function getIsOpen() {
		return $this->is_open;
	}

	/**
	 * Set share
	 *
	 * @param Share $share
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
	 * Add pm
	 *
	 * @param PrivateMessage $pm
	 */
	public function addPm(PrivateMessage $pm) {
		$this->pm[] = $pm;

		return $this;
	}

	/**
	 * Get pm
	 *
	 * @return Collection
	 */
	public function getPm() {
		return $this->pm;
	}

}
