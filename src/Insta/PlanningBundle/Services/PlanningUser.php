<?php
/**
 * Created by PhpStorm.
 * User: Rodolphe
 * Date: 16/12/2014
 * Time: 14:54
 */

namespace Insta\PlanningBundle\Services;


use Doctrine\Bundle\DoctrineBundle\Registry;
use Doctrine\DBAL\Connection;
use FOS\UserBundle\Doctrine\UserManager;
use FOS\UserBundle\Entity\User;
use Insta\PlanningBundle\Entity\Student;
use Insta\PlanningBundle\Entity\Teacher;
use Insta\PlanningBundle\Entity\Tutor;

/**
 * Class PlanningUser
 * @package Insta\PlanningBundle\Services
 */
class PlanningUser {

    /** @var Registry */
    private $doctrine;
    const  TYPE_USER = 'user';
    const  TYPE_STUDENT = 'student';
    const  TYPE_TUTOR = 'tutor';
    const  TYPE_TEACHER = 'teacher';

    /**
     * @param Registry $doctrine
     */
    function __construct(Registry $doctrine)
    {
        $this->doctrine = $doctrine;
    }

    /**
     * @param User $user
     * @param $type
     * @return bool
     * @throws \Exception
     */
    public function switchUserType( User $user, $type )
    {

        $em = $this->doctrine->getManager();

        if (!in_array($type, array(self::TYPE_STUDENT, self::TYPE_TEACHER, self::TYPE_TUTOR))) {
            throw new \Exception('Invalid new user type');
        }



        $user->setRoles(array());
        if ($user instanceof Student) {
            /** @var Student $user */
            $user->setTutor(null)->setHasComputer(null)->removeOrals()->setPromotion(null);

        } elseif ($user instanceof Tutor) {
            /** @var Tutor $user */
            $user->removeStudents();
        } elseif ($user instanceof Teacher ) {
            /** @var Teacher $user */
            $user->removeCourses();
        }

        $em->flush();

        /** @var Connection $conn */
        $conn = $this->doctrine->getConnection();
        $qb = $conn->createQueryBuilder();

        $query = $qb->update('insta_user', 'u')
            ->set('u.discr', ':type')
            ->where('u.id = :id')
            ->setParameter('id', $user->getId())
            ->setParameter('type', $type);


        $nb = $query->execute();
        if ($nb == 0) {
            throw new \Exception('No line modified (check if user is not already of type '.$type.').');
        }

        return $nb;


    }


}