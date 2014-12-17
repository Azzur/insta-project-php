<?php

namespace Insta\PlanningBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Grade
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class Grade
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(name="name", type="string", length=255)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(name="shortcut", type="string", length=255)
     */
    private $shortcut;


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
     * @return Grade
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
     * Set shortcut
     *
     * @param string $shortcut
     * @return Grade
     */
    public function setShortcut($shortcut)
    {
        $this->shortcut = $shortcut;

        return $this;
    }

    /**
     * Get shortcut
     *
     * @return string 
     */
    public function getShortcut()
    {
        return $this->shortcut;
    }
}
