<?php

namespace Insta\PlanningBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Entity\Group as BaseGroup;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Util\Canonicalizer;

/**
 * @ORM\Entity
 * @ORM\Table(name="insta_group")
 * @ORM\HasLifecycleCallbacks
 */
class Group extends BaseGroup
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, unique=true)
     */
    protected $nameCanonical;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="Insta\PlanningBundle\Entity\User", mappedBy="groups")
     */
    protected $users;

    public function __construct($name)
    {
        parent::__construct($name);
        $this->users = new ArrayCollection();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Add users
     *
     * @param User $users
     * @return Group
     */
    public function addUser(User $users)
    {
        $this->users[] = $users;

        return $this;
    }

    /**
     * Remove users
     *
     * @param User $users
     */
    public function removeUser(User $users)
    {
        $this->users->removeElement($users);
    }

    /**
     * Get users
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getUsers()
    {
        return $this->users;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updateNameCanonical() {
        $canonicalizer = new Canonicalizer();
        $this->nameCanonical = $canonicalizer->canonicalize($this->name);
    }

    /**
     * Set nameCanonical
     *
     * @param string $nameCanonical
     * @return Group
     */
    public function setNameCanonical($nameCanonical)
    {
        $this->nameCanonical = $nameCanonical;

        return $this;
    }

    /**
     * Get nameCanonical
     *
     * @return string 
     */
    public function getNameCanonical()
    {
        return $this->nameCanonical;
    }
}
