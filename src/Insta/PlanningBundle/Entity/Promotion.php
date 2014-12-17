<?php

namespace Insta\PlanningBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * Promotion
 *
 * @ORM\Table
 * @ORM\Entity(repositoryClass="PromotionRepository")
 * @ORM\HasLifecycleCallbacks
 */
class Promotion
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var Grade
     * @ORM\ManyToOne(targetEntity="Grade")
     */
    protected $grade;

    /**
     * @var string
     *
     * @ORM\Column(name="color", type="string", length=7)
     */
    protected $color;


    /**
     * @ORM\OneToMany(targetEntity="Lesson", mappedBy="promotion")
     *
     **/
    protected $lessons;


    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Student",mappedBy="promotion")
     *
     */
    protected $students;

    /**
     * @var integer
     *
     * @ORM\Column(name="name", type="integer", unique=true)
     */
    protected $name;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateStart", type="datetime")
     */
    protected $dateStart;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="dateEnd", type="datetime")
     */
    protected $dateEnd;


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
     * @return Promotion
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
     * Set dateStart
     *
     * @param \DateTime $dateStart
     * @return Promotion
     */
    public function setDateStart($dateStart)
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    /**
     * Get dateStart
     *
     * @return \DateTime 
     */
    public function getDateStart()
    {
        return $this->dateStart;
    }

    /**
     * Set dateEnd
     *
     * @param \DateTime $dateEnd
     * @return Promotion
     */
    public function setDateEnd($dateEnd)
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    /**
     * Get dateEnd
     *
     * @return \DateTime 
     */
    public function getDateEnd()
    {
        return $this->dateEnd;
    }
    /**
     * Constructor
     */
    public function __construct()
    {
        $this->lessons = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Add lessons
     *
     * @param \Insta\PlanningBundle\Entity\Lesson $lessons
     * @return Promotion
     */
    public function addLesson(\Insta\PlanningBundle\Entity\Lesson $lessons)
    {
        $this->lessons[] = $lessons;

        return $this;
    }

    /**
     * Remove lessons
     *
     * @param \Insta\PlanningBundle\Entity\Lesson $lessons
     */
    public function removeLesson(\Insta\PlanningBundle\Entity\Lesson $lessons)
    {
        $this->lessons->removeElement($lessons);
    }

    /**
     * Get lessons
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getLessons()
    {
        return $this->lessons;
    }

    /**
     * Add students
     *
     * @param \Insta\PlanningBundle\Entity\Student $students
     * @return Promotion
     */
    public function addStudent(\Insta\PlanningBundle\Entity\Student $students)
    {
        $this->students[] = $students;

        return $this;
    }

    /**
     * Remove students
     *
     * @param \Insta\PlanningBundle\Entity\Student $students
     */
    public function removeStudent(\Insta\PlanningBundle\Entity\Student $students)
    {
        $this->students->removeElement($students);
    }

    /**
     * Get students
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getStudents()
    {
        return $this->students;
    }

    /**
     * @ORM\PrePersist
     * @ORM\PreUpdate
     */
    public function updateColor() {
        $checksum = md5($this->name . time());
        $this->color = "#" .
            hexdec(substr($checksum, 0, 2)).
            hexdec(substr($checksum, 2, 2)).
            hexdec(substr($checksum, 4, 2));

    }

    /**
     * Set color
     *
     * @param string $color
     * @return Promotion
     */
    public function setColor($color)
    {
        $this->color = $color;

        return $this;
    }

    /**
     * Get color
     *
     * @return string 
     */
    public function getColor()
    {
        return $this->color;
    }

    /**
     * @param \DateTime $date
     * @return Schedule[]
     */
    public function getScheduleFor(\DateTime $date) {
        $schedules = array();
        foreach ($this->lessons as $schedule) {
            /** @var Lesson $schedule */
            if ($schedule->getDatetime()->format('Y-m-d') == $date->format('Y-m-d')  ) {
                $schedules[$schedule->getDatetime()->getTimestamp()] = $schedule;
            }
        }

        foreach ($this->students as $student) {
            /** @var Student $student */
            foreach ($student->getOrals() as $schedule) {
                /** @var Oral $schedule */
                if ($schedule->getDatetime()->format('Y-m-d') == $date->format('Y-m-d')  ) {
                    $schedules[$schedule->getDatetime()->getTimestamp()] = $schedule;
                }
            }
        }

        ksort($schedules);

        return $schedules;

    }

    /**
     * @param Schedule $inputSchedule
     * @return bool
     */
    public function isFreeFor(Schedule $inputSchedule) {
        /** @var Schedule[] $schedules */
        $schedules = $this->getLessons()->toArray();
        foreach ($this->students as $student) {
            /** @var Student $student */
            $schedules = array_merge($schedules, $student->getOrals()->toArray());
        }

        foreach ($schedules as $schedule) {
            if ( $schedule->getEndDatetime() > $inputSchedule->getDatetime() &&
            $schedule->getDatetime() < $inputSchedule->getEndDatetime() ) {
                return false;
            }
        }

        return true;

    }

    /**
     * Set grade
     *
     * @param Grade $grade
     * @return Promotion
     */
    public function setGrade(Grade $grade = null)
    {
        $this->grade = $grade;

        return $this;
    }

    /**
     * Get grade
     *
     * @return Grade
     */
    public function getGrade()
    {
        return $this->grade;
    }
}
