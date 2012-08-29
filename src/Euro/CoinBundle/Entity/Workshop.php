<?php

namespace Euro\CoinBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Euro\CoinBundle\Entity\Workshop
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Euro\CoinBundle\Entity\WorkshopRepository")
 */
class Workshop {

	/**
	 * @var integer $id
	 *
	 * @ORM\Column(name="id", type="integer")
	 * @ORM\Id
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	private $id;

	/**
	 * @var string $short_name
	 *
	 * @ORM\Column(name="short_name", type="string", length=1)
	 */
	private $short_name;

	/**
	 * @var string $name
	 *
	 * @ORM\Column(name="name", type="string", length=255)
	 */
	private $name;

	public function __toString() {
		return $this->getShortName() . ' - ' . $this->getName();
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
	 * Set short_name
	 *
	 * @param string $shortName
	 * @return Workshop
	 */
	public function setShortName($shortName) {
		$this->short_name = $shortName;

		return $this;
	}

	/**
	 * Get short_name
	 *
	 * @return string 
	 */
	public function getShortName() {
		return $this->short_name;
	}

	/**
	 * Set name
	 *
	 * @param string $name
	 * @return Workshop
	 */
	public function setName($name) {
		$this->name = $name;

		return $this;
	}

	/**
	 * Get name
	 *
	 * @return string 
	 */
	public function getName() {
		return $this->name;
	}

}
