<?php
/**
 * Created by PhpStorm.
 * User: Rodolphe
 * Date: 07/12/2014
 * Time: 17:37
 */

namespace Insta\PlanningBundle\Controller;


use Insta\PlanningBundle\Entity\Grade;
use Insta\PlanningBundle\Entity\Promotion;
use Insta\PlanningBundle\Entity\Student;
use Insta\PlanningBundle\Form\GradeType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class StudentController extends Controller {

    function indexAction() {
        $em = $this->getDoctrine();
        $students = $em->getRepository("PlanningBundle:Student");
        $promos = $em->getRepository("PlanningBundle:Promotion");
        return $this->render('PlanningBundle:Student:index.html.twig', array(
            'students'=>$students->findAll(),
            'promos'=>$promos->findAll()
        ));
    }

    public function addStudentAction() {
        $response = $this->forward('PlanningBundle:User:addUser', array('type'=>'student'));
        return $response;
    }

    public function addPromoAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $newPromo = new Promotion();
        $form = $this->createFormBuilder($newPromo)
            ->add('name', 'text',array('label' => 'promo.name'))
            ->add('grade', 'entity', array(
                'class' => 'Insta\PlanningBundle\Entity\Grade',
                'property' => 'name',
                'required'=>false,
                'label' => 'course.grade'
            ))
            ->add('dateStart', 'date',array('label' => 'promo.dateStart'))
            ->add('dateEnd', 'date',array('label' => 'promo.dateEnd'))
            ->add('form.add', 'submit')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($newPromo);
            $em->flush();
            return $this->redirectToRoute('student_promo_list');
        }

        return $this->render('PlanningBundle:Student:addPromo.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @param Request $request
     * @param Student $student
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @ParamConverter("student", options={"mapping":{"id" : "usernameCanonical"}})
     */
    public function editStudentAction(Request $request, Student $student)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createFormBuilder($student)
            ->add('firstname', 'text',array('label' => 'student.firstname'))
            ->add('lastname', 'text',array('label' => 'student.lastname'))
            ->add('hascomputer', 'checkbox',array('label' => "student.hascomputer",'required' => false))
            ->add('promotion', 'entity', array(
                'class' => 'Insta\PlanningBundle\Entity\Promotion',
                'property' => 'name',
                'label' => 'student.promotion'
            ))
            ->add('grade', 'entity', array(
                'class' => 'Insta\PlanningBundle\Entity\Grade',
                'property' => 'name',
                'required'=>false,
                'label' => 'course.grade'
            ))
            ->add('form.edit', 'submit')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('show_student', array('id'=>$student->getUsernameCanonical()));
        }

        return $this->render('PlanningBundle:Student:editStudent.html.twig', array(
            'form'=>$form->createView(),
            'student'=>$student
        ));
    }

    public function studentListAction($offset)
    {
        $repo = $this->getDoctrine()->getManager()->getRepository('PlanningBundle:Student');;
        $nbPage = (int) floor(count($repo->findAll()) /20);
        $students = $repo->findBy(array(),null, 20, $offset * 20);;
        return $this->render('PlanningBundle:Student:student_list.html.twig', array(
            'students' => $students,
            'nbPages' => $nbPage,
            'offset' => $offset
        ));
    }

    public function editPromoAction(Request $request, Promotion $id)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createFormBuilder($id)
            ->add('name', 'text',array('label' => 'promo.name'))
            ->add('grade', 'entity', array(
                'class' => 'Insta\PlanningBundle\Entity\Grade',
                'property' => 'name',
                'required'=>false,
                'label' => 'course.grade'
            ))
            ->add('dateStart', 'date',array('label' => 'promo.dateStart'))
            ->add('dateEnd', 'date',array('label' => 'promo.dateEnd'))
            ->add('form.edit', 'submit')
            ->getForm();
        $form->handleRequest($request);
        if ($form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('show_promo', array('id'=>$id->getId()));
        }
        return $this->render('PlanningBundle:Student:editPromo.html.twig', array(
            'form'=>$form->createView(),
            'promo'=>$id
        ));
    }

    /**
     * @param Student $student
     * @internal param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @ParamConverter("student", options={"mapping":{"id" : "usernameCanonical"}})
     */
    public function showStudentAction(Student $student)
    {
        return $this->render('PlanningBundle:Student:showStudent.html.twig', array(
            'student'=>$student
        ));
    }

    /**
     * @param Student $student
     * @internal param Request $request
     * @internal param Promotion $promoId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @ParamConverter("student", options={"mapping":{"id" : "usernameCanonical"}})
     * @ParamConverter("promotion", options={"mapping":{"promoid" : "id"}})
     */
    public function removeStudentFromPromoAction(Student $student)
    {
        $em = $this->getDoctrine()->getManager();
        $student -> setPromotion(null);
        $em->flush();
        return $this->redirectToRoute('student_promo_list');
    }

    public function showPromoAction(Promotion $promo)
    {
        return $this->render('PlanningBundle:Student:showPromo.html.twig', array(
            'promo'=>$promo
        ));
    }

    public function listGradeAction() {

        $em = $this->getDoctrine()->getManager();
        $grades = $em->getRepository('PlanningBundle:Grade')->findAll();

        return $this->render('PlanningBundle:Student:listGrades.html.twig', array(
            'grades' => $grades
        ));

    }

    public function editGradeAction(Request $request, $id) {

        $em = $this->getDoctrine()->getManager();
        $grade = $em->getRepository('PlanningBundle:Grade')->find($id);

        if (is_null($grade)) {
            return $this->createNotFoundException('Formation non trouvée.');
        }

        $form = $this->createForm(new GradeType(), $grade)
            ->add('edit', 'submit', array('label'=>'form.edit'));

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->flush();
            return $this->redirectToRoute('list_grade');
        }

        return $this->render('PlanningBundle:Student:editGrades.html.twig', array(
            'form' => $form->createView()
        ));

    }



    public function addGradeAction(Request $request) {

        $em = $this->getDoctrine()->getManager();
        $grade = new Grade();

        $form = $this->createForm(new GradeType(), $grade)
            ->add('add', 'submit', array('label'=>'form.add'));

        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($grade);
            $em->flush();
            return $this->redirectToRoute('list_grade');
        }

        return $this->render('PlanningBundle:Student:editGrades.html.twig', array(
            'form' => $form->createView()
        ));

    }
} 