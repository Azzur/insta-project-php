<?php
/**
 * Created by PhpStorm.
 * User: Rodolphe
 * Date: 07/12/2014
 * Time: 17:38
 */

namespace Insta\PlanningBundle\Controller;

use Doctrine\ORM\EntityManager;
use Insta\PlanningBundle\Entity\Course;
use Insta\PlanningBundle\Entity\Lesson;
use Insta\PlanningBundle\Entity\Oral;
use Insta\PlanningBundle\Entity\Teacher;
use Insta\PlanningBundle\Form\CourseType;
use Insta\PlanningBundle\Form\LessonType;
use Insta\PlanningBundle\Form\OralType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
class ScheduleController extends Controller {

    function createLessonAction(Request $request) {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $scheduleRepo = $em->getRepository('PlanningBundle:Schedule');

        $lesson = new Lesson();
        $form = $this->createForm(new LessonType(), $lesson);
        $defaultDate = new \DateTime('2001-01-01');
        $defaultDate -> setTime(0,01,0);
        $form->get('duration')->setData($defaultDate);

        $form->handleRequest($request);

        if ($form->isValid()) {
            // Convert to hours to minute
            $t = explode(":", $form->get('duration')->getData()->format('H:i'));
            $h = $t[0];
            if (isset($t[1])) {
                $m = $t[1];
            } else {
                $m = "00";
            }
            $mm = ($h * 60) +  $m;
            $lesson->setDuration($mm);
            if ($scheduleRepo->isRoomUsed($lesson)) {

                $freeRooms = $scheduleRepo->getEmptyRooms($lesson);

                $strInfo = '';
                if (count($freeRooms) > 0) {
                    $first = true;
                    foreach ($freeRooms as $room) {
                        $strInfo = ($first) ? $strInfo . $room->getName() :$strInfo .', '. $room->getName();
                        $first = false;
                    }
                    $strInfo = 'Salle(s) libre(s) : ' .$strInfo;
                } else {
                    $strInfo = 'Pas de salle disponible pour ce créneau.';
                }

                $form->get('room')->addError(new FormError('La salle est déjà utilisée pour ce créneau. ' .$strInfo));
            }

            if (!$lesson->getPromotion()->isFreeFor($lesson)) {
                $form->get('promotion')->addError(new FormError('La promotion participe déjà à un autre évènement.'));
            }

            $teacherFree = false;
            foreach ($lesson->getCourse()->getTeachers() as $teacher) {
                /** @var Teacher $teacher */
                if ($teacher->isFreeFor($lesson)) {
                    $teacherFree = true;
                }
            }

            if (!$teacherFree) {
                $form->get('course')->addError(new FormError('Le professeur participe déjà à un autre évènement.'));
            }

            /** @var Lesson[] $toPersist */
            $toPersist = array();

            if ($form->get('periodicity')->getData()) {

                for ($i = 0 ; $i < $form->get('nb_repetition')->getData() ; $i++) {

                    $periodLesson = clone $lesson;
                    $periodDate   = clone $lesson->getDatetime();
                    $periodDate->modify("+$i weeks");
                    $periodLesson->setDatetime( $periodDate );

                    if ($scheduleRepo->isRoomUsed($periodLesson)) {
                        $form->get('room')->addError(new FormError('La salle est déjà utilisée pour ce créneau le : ' . $periodDate->format('d/m/Y')));
                    }

                    if (!$periodLesson->getPromotion()->isFreeFor($periodLesson)) {
                        $form->get('promotion')->addError(new FormError('La promotion participe déjà à un autre évènement le : ' . $periodDate->format('d/m/Y')));
                    }

                    $toPersist[] = ($periodLesson);

                }

            } else {
                $toPersist[] = ($lesson);
            }



            if ($form->getErrors(true)->count() == 0) {
                foreach($toPersist as $entity) {
                    $em->persist($entity);
                }
                $em->flush();

                return $this->redirectToRoute('show_schedule', array('id'=>$toPersist[0]->getId()));

            }


        }

        return $this->render('PlanningBundle:Schedule:create_schedule.html.twig', array( 'form'=>$form->createView() ));

    }

    function createOralAction(Request $request) {

        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();
        $scheduleRepo = $em->getRepository('PlanningBundle:Schedule');

        $oral = new Oral();
        $form = $this->createForm(new OralType(), $oral);
        $defaultDate = new \DateTime('2001-01-01');
        $defaultDate -> setTime(0,01,0);
        $form->get('duration')->setData($defaultDate);

        $form->handleRequest($request);

        if ($form->isValid()) {
            // Convert to hours to minute
            $t = explode(":", $form->get('duration')->getData()->format('H:i'));
            $h = $t[0];
            if (isset($t[1])) {
                $m = $t[1];
            } else {
                $m = "00";
            }
            $mm = ($h * 60) +  $m;
            $oral->setDuration($mm);
            if ($scheduleRepo->isRoomUsed($oral)) {

                $freeRooms = $scheduleRepo->getEmptyRooms($oral);

                $strInfo = '';
                if (count($freeRooms) > 0) {
                    $first = true;
                    foreach ($freeRooms as $room) {
                        $strInfo = ($first) ? $strInfo . $room->getName() :$strInfo .', '. $room->getName();
                        $first = false;
                    }
                    $strInfo = 'Salle(s) libre(s) : ' .$strInfo;
                } else {
                    $strInfo = 'Pas de salle disponible pour ce créneau.';
                }

                $form->get('room')->addError(new FormError('La salle est déjà utilisée pour ce créneau. ' .$strInfo));
            }

            $teacherFree = false;
            foreach ($oral->getCourse()->getTeachers() as $teacher) {
                /** @var Teacher $teacher */
                if ($teacher->isFreeFor($oral)) {
                    $teacherFree = true;
                }
            }

            if (!$teacherFree) {
                $form->get('course')->addError(new FormError('Le professeur participe déjà à un autre évènement.'));
            }

            foreach ($oral->getStudents() as $student) {

                if (is_null($student->getPromotion())) {
                    $form->get('students')->addError(new FormError( $student->getFullname() . ' ne fait pas partie d\'une promotion.' ));
                } elseif (!$student->getPromotion()->isFreeFor($oral)) {
                    $form->get('students')->addError(new FormError('L\'élève '.$student->getFullname().' participe déjà à un autre évènement.'));
                }

            }

            if ($form->getErrors(true)->count() == 0){

                $em->persist($oral);
                $em->flush();

                return $this->redirectToRoute('show_schedule', array('id'=>$oral->getId()));
            }

        }

        return $this->render('PlanningBundle:Schedule:create_schedule.html.twig', array( 'form'=>$form->createView() ));

    }

    function manageCourseAction(Request $request, Course $course) {

        $em = $this->getDoctrine()->getManager();

        $form = $this->createForm(new CourseType(), $course);
        $form->remove('name')->remove('teachers')->add('edit', 'submit', array('label'=>'form.edit'));

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('manage_course', array('course'=>$course->getId()));
        }

        return $this->render('PlanningBundle:Schedule:manage_course.html.twig', array('form'=>$form->createView(), 'course'=>$course));

    }

} 