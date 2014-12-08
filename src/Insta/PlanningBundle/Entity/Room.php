<?php

namespace Insta\PlanningBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Room
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Room
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
     * @ORM\ManyToOne(targetEntity="Site", inversedBy="rooms")
     * @ORM\JoinColumn(name="site_id", referencedColumnName="id")
     **/

    protected $site;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    protected $name;

    /**
     * @var string
     *
     * @ORM\Column(name="computer_nb", type="integer")
     */
    protected $freeComputer;


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
     * @return Room
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
     * Set site
     *
     * @param \Insta\PlanningBundle\Entity\Site $site
     * @return Room
     */
    public function setSite(\Insta\PlanningBundle\Entity\Site $site = null)
    {
        $this->site = $site;

        return $this;
    }

    /**
     * Get site
     *
     * @return \Insta\PlanningBundle\Entity\Site 
     */
    public function getSite()
    {
        return $this->site;
    }

    /**
     * Set freeComputer
     *
     * @param integer $freeComputer
     * @return Room
     */
    public function setFreeComputer($freeComputer)
    {
        $this->freeComputer = $freeComputer;

        return $this;
    }

    /**
     * Get freeComputer
     *
     * @return integer 
     */
    public function getFreeComputer()
    {
        return $this->freeComputer;
    }
}
