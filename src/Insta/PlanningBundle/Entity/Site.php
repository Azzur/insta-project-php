<?php

namespace Insta\PlanningBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Site
 *
 * @ORM\Table()
 * @ORM\Entity
 */


class Site
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    // ...
    /**
     * @ORM\OneToMany(targetEntity="Room", mappedBy="site")
     **/

    protected $rooms;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var integer
     *
     * @ORM\Column(name="zipCode", type="integer")
     */
    protected $zipCode;

    /**
     * @var string
     *
     * @ORM\Column(name="street", type="string", length=255)
     */
    protected $street;

    /**
     * @var string
     *
     * @ORM\Column(name="city", type="string", length=255)
     */
    protected $city;

    /**
     * @var string
     *
     * @ORM\Column(name="phoneNumber", type="string", length=255)
     */
    protected $phoneNumber;

    /**
     * @var array
     *
     * @ORM\Column(name="subwayLines", type="array")
     */
    protected $subwayLines;

    /**
     * @var string
     *
     * @ORM\Column(name="subwayStop", type="string", length=255)
     */
    protected $subwayStop;


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
     * Set name
     *
     * @param string $name
     * @return Site
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set zipCode
     *
     * @param integer $zipCode
     * @return Site
     */
    public function setZipCode($zipCode)
    {
        $this->zipCode = $zipCode;

        return $this;
    }

    /**
     * Get zipCode
     *
     * @return integer 
     */
    public function getZipCode()
    {
        return $this->zipCode;
    }

    /**
     * Set street
     *
     * @param string $street
     * @return Site
     */
    public function setStreet($street)
    {
        $this->street = $street;

        return $this;
    }

    /**
     * Get street
     *
     * @return string 
     */
    public function getStreet()
    {
        return $this->street;
    }

    /**
     * Set city
     *
     * @param string $city
     * @return Site
     */
    public function setCity($city)
    {
        $this->city = $city;

        return $this;
    }

    /**
     * Get city
     *
     * @return string 
     */
    public function getCity()
    {
        return $this->city;
    }

    /**
     * Set phoneNumber
     *
     * @param string $phoneNumber
     * @return Site
     */
    public function setPhoneNumber($phoneNumber)
    {
        $this->phoneNumber = $phoneNumber;

        return $this;
    }

    /**
     * Get phoneNumber
     *
     * @return string 
     */
    public function getPhoneNumber()
    {
        return $this->phoneNumber;
    }

    /**
     * Set subwayLines
     *
     * @param array $subwayLines
     * @return Site
     */
    public function setSubwayLines($subwayLines)
    {
        $this->subwayLines = $subwayLines;

        return $this;
    }

    /**
     * Get subwayLines
     *
     * @return array 
     */
    public function getSubwayLines()
    {
        return $this->subwayLines;
    }

    /**
     * Set subwayStop
     *
     * @param string $subwayStop
     * @return Site
     */
    public function setSubwayStop($subwayStop)
    {
        $this->subwayStop = $subwayStop;

        return $this;
    }

    /**
     * Get subwayStop
     *
     * @return string 
     */
    public function getSubwayStop()
    {
        return $this->subwayStop;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->rooms = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add rooms
     *
     * @param \Insta\PlanningBundle\Entity\Room $rooms
     * @return Site
     */
    public function addRoom(\Insta\PlanningBundle\Entity\Room $rooms)
    {
        $this->rooms[] = $rooms;

        return $this;
    }

    /**
     * Remove rooms
     *
     * @param \Insta\PlanningBundle\Entity\Room $rooms
     */
    public function removeRoom(\Insta\PlanningBundle\Entity\Room $rooms)
    {
        $this->rooms->removeElement($rooms);
    }

    /**
     * Get rooms
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getRooms()
    {
        return $this->rooms;
    }
}
