<?php
/**
 * Created by PhpStorm.
 * User: Rodolphe
 * Date: 07/12/2014
 * Time: 17:36
 */

namespace Insta\PlanningBundle\Controller;


use Insta\PlanningBundle\Entity\Course;
use Insta\PlanningBundle\Entity\Teacher;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class TeacherController extends Controller
{

    public function indexAction()
    {

        $tm = $this->getDoctrine()->getManager();

        $teacher = $tm->getRepository('PlanningBundle:Teacher')->findAll();
        $course = $tm->getRepository('PlanningBundle:Course')->findAll();

        return $this->render('PlanningBundle:Teacher:index.html.twig', array(
            'courses' => $course,
            'teachers' => $teacher,
        ));
    }

    public function addTeacherAction() {
        $response = $this->forward('PlanningBundle:User:addUser', array('type'=>'teacher'));
        return $response;
    }

    /**
     * @param Teacher $teacher
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @ParamConverter("teacher", options={"mapping": {"name" : "usernameCanonical"} })
     */
    public function teacherAction(Teacher $teacher)
    {
        return $this->render('PlanningBundle:Teacher:teacher.html.twig', array(
            'teacher'=> $teacher
        ));    }

    public function courseAction(Course $course)
    {
        return $this->render('PlanningBundle:Teacher:course.html.twig', array(
            'course'=> $course
        ));    }


    public function teacherListAction()
    {
        $tm = $this->getDoctrine()->getManager();
        $teachers = $tm->getRepository('PlanningBundle:Teacher')->findAll();
        return $this->render('PlanningBundle:Teacher:listTeacher.html.twig', array(
            'teachers' => $teachers
        ));
    }

    public function courseListAction()
    {
        $cm = $this->getDoctrine()->getManager();
        $courses = $cm->getRepository('PlanningBundle:Course')->findAll();
        return $this->render('PlanningBundle:Teacher:courseList.html.twig', array(
            'courses' => $courses
        ));
    }

    /**
     * @param Teacher $teacher
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @ParamConverter("teacher", options={"mapping":{"name" : "usernameCanonical"}})
     */
    public function deleteTeacherAction(Teacher $teacher)
    {
        $tm = $this->getDoctrine()->getManager();
        $tm -> remove($teacher);
        $tm -> flush();
        return $this->redirectToRoute('teacher_list');
    }

    /**
     * @param Course $course
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     * @ParamConverter("course", options={"mapping":{"id" : "id"}})
     */
    public function deleteCourseAction(Course $course)
    {
        $cm = $this->getDoctrine()->getManager();
        $cm -> remove($course);
        $cm -> flush();
        return $this->redirectToRoute('course_list');
    }

    /**
     * @param Request $request
     * @param Teacher $teacher
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @ParamConverter("teacher", options={"mapping":{"name" : "usernameCanonical"}})
     */
    public function editTeacherAction(Request $request, Teacher $teacher)
    {
        $tm = $this->getDoctrine()->getManager();


        $form = $this->createFormBuilder($teacher)
            ->add('courses','entity', array(
                'class' => 'Insta\PlanningBundle\Entity\Course',
                'empty_value' => 'Choisissez une option',
                'property' => 'name',
                'multiple'=>true,
                'expanded'=>true,
                'label' => 'teacher.course'
            ))
            ->add('edit', 'submit',array('label' => "form.edit"))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {

            $tm->flush();
            return $this->redirectToRoute('teacher', array('name'=>$teacher->getUsernameCanonical()));

        }

        return $this->render('PlanningBundle:Teacher:editTeacher.html.twig', array(
            'form'=>$form->createView(),
            'teacher'=>$teacher
        ));    }

    public function addCourseAction(Request $request)
    {

        $cm = $this->getDoctrine()->getManager();

        $newCourse = new Course();
        $form = $this->createFormBuilder($newCourse)
            ->add('name', 'text',array('label' => 'course.name'))
            ->add('description', 'textarea',array('label' => 'course.description'))
            ->add('teachers', 'entity', array(
                'class' => 'Insta\PlanningBundle\Entity\Teacher',
                'property' => 'fullname',
                'multiple'=> true, 'expanded'=>true,
                'label' => 'course.teacher'
            ))
            ->add('add', 'submit',array('label' => "form.add"))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $cm->persist($newCourse);
            $cm->flush();
            return $this->redirectToRoute('course_list');
        }

        return $this->render('PlanningBundle:Teacher:addCourse.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @param Request $request
     * @param Course $course
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @ParamConverter("course", options={"mapping":{"id" : "id"}})
     */
    public function editCourseAction(Request $request, Course $course)
    {
        $cm = $this->getDoctrine()->getManager();


        $form = $this->createFormBuilder($course)
            ->add('name','text',array('label' => 'course.name'))
            ->add('description','textarea',array('label' => 'course.description'))
            ->add('descriptionLink', 'url', array('required'=>false,'label' => 'course.descriptionLink'))
            ->add('teachers','entity', array(
                'class' => 'Insta\PlanningBundle\Entity\Teacher',
                'property' => 'fullname',
                'multiple'=> true, 'expanded'=>true,
                'label' => 'course.teacher'
            ))
            ->add('edit', 'submit',array('label' => "form.edit"))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {

            foreach ($course->getTeachers() as $teacher) {
                /** @var Teacher $teacher */
                $teacher->addCourse($course);
            }

            $cm->flush();

            return $this->redirectToRoute('course', array('id'=>$course->getId()));

        }

        return $this->render('PlanningBundle:Teacher:editCourse.html.twig', array(
            'form'=>$form->createView(),
            'course'=>$course
        ));
    }


}