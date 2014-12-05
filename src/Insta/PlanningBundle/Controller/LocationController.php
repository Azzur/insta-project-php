<?php

namespace Insta\PlanningBundle\Controller;

use Insta\PlanningBundle\Entity\Site;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;

class LocationController extends Controller
{
    public function indexAction()
    {

        $em = $this->getDoctrine()->getManager();

        $sites = $em->getRepository('PlanningBundle:Site')->findAll();
        $rooms = $em->getRepository('PlanningBundle:Room')->findAll();


        return $this->render('PlanningBundle:Location:index.html.twig', array(
                'sites' => $sites,
                'rooms' => $rooms
            ));    }

    public function siteListAction()
    {
        return $this->render('PlanningBundle:Location:siteList.html.twig', array(
                // ...
            ));    }

    public function roomListAction()
    {
        return $this->render('PlanningBundle:Location:roomList.html.twig', array(
                // ...
            ));    }

    public function siteAction(Site $id)
    {
        return $this->render('PlanningBundle:Location:site.html.twig', array(
                'site'=> $id
            ));    }

    public function roomAction($id)
    {
        return $this->render('PlanningBundle:Location:room.html.twig', array(
                // ...
            ));    }

    public function addSiteAction(Request $request)
    {

        $em = $this->getDoctrine()->getManager();

        $newSite = new Site();
        $form = $this->createFormBuilder($newSite)
            ->add('name', 'text')
            ->add('street', 'text')
            ->add('zipCode', 'integer')
            ->add('city', 'text')
            ->add('phoneNumber', 'text', array('max_length'=>12))
            ->add('subwayStop', 'text')
            ->add('subwayLines', 'text')
            ->add('Add', 'submit')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {

            $subwayLines = array_map('trim', array_filter( explode(',', $newSite->getSubwayLines())));

            $subwayLinesWithUrl = array();

            foreach( $subwayLines as $line )  {
                $subwayLinesWithUrl[$line] = 'http://www.ratp.fr/fr/upload/docs/image/png/2014-02/footer-ligne'.strtolower($line).'.png';
            }


            $newSite->setSubwayLines($subwayLinesWithUrl);

            $em->persist($newSite);
            $em->flush();
            return $this->redirectToRoute('site', array('id'=>$newSite->getId()));

        }

        return $this->render('PlanningBundle:Location:addSite.html.twig', array(
                'form' => $form->createView()
            ));    }

    public function addRoomAction()
    {
        return $this->render('PlanningBundle:Location:addRoom.html.twig', array(
                // ...
            ));    }

    public function editSiteAction(Request $request, Site $id)
    {
        $id->setSubwayLines( implode(',', array_keys($id->getSubwayLines())) );
        $em = $this->getDoctrine()->getManager();

        $form = $this->createFormBuilder($id)
            ->add('name', 'text')
            ->add('street', 'text')
            ->add('zipCode', 'integer')
            ->add('city', 'text')
            ->add('phoneNumber', 'text', array('max_length'=>12))
            ->add('subwayStop', 'text')
            ->add('subwayLines', 'text')
            ->add('Edit', 'submit')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {

            $subwayLines = array_map('trim', array_filter( explode(',', $id->getSubwayLines())));

            $subwayLinesWithUrl = array();

            foreach( $subwayLines as $line )  {
                $subwayLinesWithUrl[$line] = 'http://www.ratp.fr/fr/upload/docs/image/png/2014-02/footer-ligne'.strtolower($line).'.png';
            }


            $id->setSubwayLines($subwayLinesWithUrl);

            $em->flush();
            return $this->redirectToRoute('site', array('id'=>$id->getId()));

        }


        return $this->render('PlanningBundle:Location:addSite.html.twig', array(
                'form'=>$form->createView()
            ));    }

    public function editRoomAction($id)
    {
        return $this->render('PlanningBundle:Location:editRoom.html.twig', array(
                // ...
            ));    }

}
