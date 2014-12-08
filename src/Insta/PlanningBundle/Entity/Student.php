<?php

namespace Insta\PlanningBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\GroupInterface;

/**
 * Student
 *
 * @ORM\Entity
 *
 */
class Student extends User
{


    /**
     * @var boolean
     *
     * @ORM\Column(name="hasComputer", type="boolean")
     */
    protected $hasComputer;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToOne(targetEntity="Promotion")
     * @ORM\JoinColumn(name="promotion_id", referencedColumnName="id")
     */
    protected $promotion;


    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToOne(targetEntity="Tutor", inversedBy="students")
     * @ORM\JoinColumn(name="tutor_id", referencedColumnName="id")
     */
    protected $tutor;

    /**
     *
     * @var ArrayCollection
     * @ORM\ManyToMany(targetEntity="Oral", inversedBy="students")
     **/
    protected $orals;

    /**
     * Set hasComputer
     *
     * @param boolean $hasComputer
     * @return Student
     */

    public function setHasComputer($hasComputer)
    {
        $this->hasComputer = $hasComputer;

        return $this;
    }

    /**
     * Get hasComputer
     *
     * @return boolean 
     */
    public function getHasComputer()
    {
        return $this->hasComputer;
    }
    /**
     * @var integer
     */
    protected  $id;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    protected $groups;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->groups = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set promotion
     *
     * @param \Insta\PlanningBundle\Entity\Promotion $promotion
     * @return Student
     */
    public function setPromotion(\Insta\PlanningBundle\Entity\Promotion $promotion = null)
    {
        $this->promotion = $promotion;

        return $this;
    }

    /**
     * Get promotion
     *
     * @return \Insta\PlanningBundle\Entity\Promotion 
     */
    public function getPromotion()
    {
        return $this->promotion;
    }

    /**
     * Add groups
     *
     * @param \Insta\PlanningBundle\Entity\Group $groups
     * @return Student
     */
    public function addGroup(GroupInterface $groups)
    {
        $this->groups[] = $groups;

        return $this;
    }

    /**
     * Remove groups
     *
     * @param \Insta\PlanningBundle\Entity\Group $groups
     */
    public function removeGroup(GroupInterface $groups)
    {
        $this->groups->removeElement($groups);
    }

    /**
     * Get groups
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getGroups()
    {
        return $this->groups;
    }

    /**
     * Set tutor
     *
     * @param \Insta\PlanningBundle\Entity\Tutor $tutor
     * @return Student
     */
    public function setTutor(\Insta\PlanningBundle\Entity\Tutor $tutor = null)
    {
        $this->tutor = $tutor;

        return $this;
    }

    /**
     * Get tutor
     *
     * @return \Insta\PlanningBundle\Entity\Tutor 
     */
    public function getTutor()
    {
        return $this->tutor;
    }

    /**
     * Add orals
     *
     * @param \Insta\PlanningBundle\Entity\Oral $orals
     * @return Student
     */
    public function addOral(\Insta\PlanningBundle\Entity\Oral $orals)
    {
        $this->orals[] = $orals;

        return $this;
    }

    /**
     * Remove orals
     *
     * @param \Insta\PlanningBundle\Entity\Oral $orals
     */
    public function removeOral(\Insta\PlanningBundle\Entity\Oral $orals)
    {
        $this->orals->removeElement($orals);
    }

    /**
     * Get orals
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getOrals()
    {
        return $this->orals;
    }
}
