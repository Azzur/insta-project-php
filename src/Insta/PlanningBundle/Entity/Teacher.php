<?php

namespace Insta\PlanningBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use FOS\UserBundle\Model\GroupInterface;

/**
 * Teacher
 *
 * @ORM\Entity(repositoryClass = "TeacherRepository")
 */
class Teacher extends User
{
    /**
     * @ORM\ManyToMany(targetEntity="Course", inversedBy="teachers")
     * @ORM\JoinTable(name="teacher_course",
     *      joinColumns={@ORM\JoinColumn(name="teacher_id", referencedColumnName="id", onDelete="CASCADE")},
     *      inverseJoinColumns={@ORM\JoinColumn(name="course_id", referencedColumnName="id", onDelete="CASCADE")}
     *
     *      )
     **/
    protected $courses;
    /**
     * @var integer
     */
    protected $id;

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
        $this->courses = new ArrayCollection();
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
     * Add courses
     *
     * @param Course $courses
     * @return Teacher
     */
    public function addCourse(Course $courses)
    {
        $this->courses[] = $courses;

        return $this;
    }

    /**
     * Remove courses
     *
     * @param Course $courses
     */
    public function removeCourse(Course $courses)
    {
        $this->courses->removeElement($courses);
    }

    /**
     * Get courses
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getCourses()
    {
        return $this->courses;
    }

    /**
     * Add groups
     *
     * @param \Insta\PlanningBundle\Entity\Group $groups
     * @return Teacher
     */
    public function addGroup(GroupInterface $groups)
    {
        $this->groups[] = $groups;

        return $this;
    }

    /**
     * Remove groups
     *
     * @param GroupInterface|Group $groups
     * @return $this|void
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
     * @return Teacher
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
     * @return Teacher
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

    public function removeCourses()
    {
        foreach ($this->courses->toArray() as $course) {
            $this->courses->removeElement($course);
        }

        return $this;
    }

    public function getRoles(){
        return array_merge(parent::getRoles(), array('ROLE_TEACHER'));
    }

    public function getCourseNames()
    {
        $names=array();
        $courses=$this->getCourses();
        foreach($courses as $course)
        {
            $names[]=$course->getName();
        }
        return $names;
    }

    public function isFreeFor(Schedule $inputSchedule) {

        /** @var Schedule[] $schedules */
        $schedules = array();
        foreach ($this->courses as $course) {
            /** @var Course $course */
            $schedules = array_merge($schedules, $course->getOrals()->toArray());
            $schedules = array_merge($schedules, $course->getLessons()->toArray());
        }

        foreach ($schedules as $schedule) {
            if ( $schedule->getEndDatetime() > $inputSchedule->getDatetime() &&
                $schedule->getDatetime() < $inputSchedule->getEndDatetime() ) {
                return false;
            }
        }

        return true;

    }
}
