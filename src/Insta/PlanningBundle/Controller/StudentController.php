<?php
/**
 * Created by PhpStorm.
 * User: Rodolphe
 * Date: 07/12/2014
 * Time: 17:37
 */

namespace Insta\PlanningBundle\Controller;


use Insta\PlanningBundle\Entity\Promotion;
use Insta\PlanningBundle\Entity\Student;
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
            ->add('name', 'text')
            ->add('dateStart', 'date')
            ->add('dateEnd', 'date')
            ->add('Add', 'submit')
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
            ->add('firstname', 'text')
            ->add('lastname', 'text')
            ->add('hascomputer', 'checkbox',array('required' => false))
            ->add('promotion', 'entity', array(
                'class' => 'Insta\PlanningBundle\Entity\Promotion',
                'property' => 'name',
            ))
            ->add('Edit', 'submit')
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

    public function editPromoAction(Request $request, Promotion $id)
    {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createFormBuilder($id)
            ->add('name', 'text')
            ->add('dateStart', 'date')
            ->add('dateEnd', 'date')
            ->add('Edit', 'submit')
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
     * @param Request $request
     * @param Student $student
     * @param Promotion $promoId
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     *
     * @ParamConverter("student", options={"mapping":{"id" : "usernameCanonical"}})
     * @ParamConverter("promotion", options={"mapping":{"promoid" : "id"}})
     */
    public function removeStudentFromPromoAction(Student $student,Promotion $promotion)
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
} 