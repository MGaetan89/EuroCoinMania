<?php

namespace Euro\UserBundle\Entity;

use FOS\UserBundle\Entity\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * Euro\UserBundle\Entity\User
 *
 * @ORM\Table(name="members")
 * @ORM\Entity(repositoryClass="Euro\UserBundle\Entity\UserRepository")
 */
class User extends BaseUser {
	/**
	 * @ORM\Id
	 * @ORM\Column(type="integer")
	 * @ORM\GeneratedValue(strategy="AUTO")
	 */
	protected $id;

}
