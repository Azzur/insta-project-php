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
     * @var Promotion
     *
     * @ORM\ManyToOne(targetEntity="Promotion", inversedBy="students")
     * @ORM\JoinColumn(name="promotion_id", referencedColumnName="id")
     */
    protected $promotion;


    /**
     * @var Tutor
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
        parent::__construct();
        $this->setHasComputer(true);
        $this->orals = new ArrayCollection();
        $this->groups = new ArrayCollection();
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
    /**
     * @var string
     */
    protected $firstname;

    /**
     * @var string
     */
    protected $lastname;


    /**
     * Set firstname
     *
     * @param string $firstname
     * @return Student
     */
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get firstname
     *
     * @return string 
     */
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set lastname
     *
     * @param string $lastname
     * @return Student
     */
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get lastname
     *
     * @return string 
     */
    public function getLastname()
    {
        return $this->lastname;
    }
}
