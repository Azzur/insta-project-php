<?php
/**
 * Created by PhpStorm.
 * User: Rodolphe
 * Date: 07/12/2014
 * Time: 18:05
 */

namespace Insta\PlanningBundle\Controller;


use Insta\PlanningBundle\Entity\Group;
use Insta\PlanningBundle\Entity\Student;
use Insta\PlanningBundle\Entity\Teacher;
use Insta\PlanningBundle\Entity\Tutor;
use Insta\PlanningBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;

class UserController extends Controller {

    const  TYPE_USER = 'user';
    const  TYPE_STUDENT = 'student';
    const  TYPE_TUTOR = 'tutor';
    const  TYPE_TEACHER = 'teacher';

    function indexAction() {

        $um = $this->get('fos_user.user_manager');
        $gm = $this->get('fos_user.group_manager');

        return $this->render('PlanningBundle:User:index.html.twig', array(
            'users'=>$um->findUsers(),
            'groups'=>$gm->findGroups()
        ));
    }

    function userListAction($offset) {
        $repo = $this->getDoctrine()->getRepository('PlanningBundle:User');
        $nbPage = (int) floor(count($repo->findAll()) /20);
        $users = $repo->findBy(array(),null, 20, $offset * 20);
        return $this->render('PlanningBundle:User:user_list.html.twig', array(
            'users'=>$users,
            'nbPages' => $nbPage,
            'offset' => $offset
        ));
    }

    function addUserAction(Request $request, $type) {
        $um = $this->get('fos_user.user_manager');

        $form = $this->createFormBuilder()
            ->add('firstname', 'text')
            ->add('lastname', 'text')
            ->add('username', 'text')
            ->add('email', 'email')
            ->add('type', 'choice', array(
                'choices' => array(
                    self::TYPE_USER => 'Aucun type spécifique / Administration',
                    self::TYPE_STUDENT => 'Elève',
                    self::TYPE_TUTOR => 'Tuteur',
                    self::TYPE_TEACHER => 'Professeur'),
                'expanded' => true,
                'multiple' => false,
                'data' => $type,
                'disabled' => ($type !== 'none')
            ))
            ->add('Ajouter', 'submit')
            ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->isValid()) {


            $data = $form->getData();

            $type = (isset($data['type'])) ? ($data['type']) : $type;

            switch ($type) {

                case self::TYPE_STUDENT:
                    $user = new Student();
                    break;
                case self::TYPE_TEACHER:
                    $user = new Teacher();
                    break;
                case self::TYPE_TUTOR:
                    $user = new Tutor();
                    break;
                case self::TYPE_USER : default :
                    $user = $um->createUser();
                    break;

            }

            /** @var User $user */
            $user->setFirstname($data['firstname']);
            $user->setPassword(uniqid());
            $user->setLastname($data['lastname']);
            $user->setEmail($data['email']);
            $user->setUsername($data['username']);

            $um->updateUser($user);
            return $this->redirectToRoute('user_group_list');
        }


        return $this->render('PlanningBundle:User:add_user.html.twig', array(
            'form' => $form->createView()
        ));

    }

    /**
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    function addGroupAction(Request $request) {
        $gm = $this->get('fos_user.group_manager');

        $group = $gm->createGroup('');

        $form = $this->createForm('fos_user_group', $group)
            ->add('Ajouter', 'submit');

        $form->handleRequest($request);

        if ($form->isValid()) {
            $gm->updateGroup($group);

            return $this->redirectToRoute('user_group_list');
        }

        return $this->render('PlanningBundle:User:add_group.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @ParamConverter("user", options={"mapping": {"user": "usernameCanonical"}})
     * @param Request $request
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    function showUserAction(Request $request, User $user) {

        return $this->render('PlanningBundle:User:show_user.html.twig', array(
            'user' => $user
        ));

    }

    /**
     * @ParamConverter("user", options={"mapping": {"user": "usernameCanonical"}})
     * @param Request $request
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    function editUserAction(Request $request, User $user) {

        $um = $this->get('fos_user.user_manager');

        $form = $this->createFormBuilder($user)
            ->add('username', 'text')
            ->add('firstname', 'text')
            ->add('lastname', 'text')
            ->add('email', 'email')
            ->add('groups', 'entity', array(
                'class' => 'Insta\PlanningBundle\Entity\Group',
                'property' => 'name',
                'multiple' => true,
                'expanded' => true
            ))
            ->add('locked', 'checkbox', array('required'=>false))
            ->add('Editer', 'submit')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $um->updateUser($user);

            return $this->redirectToRoute('user_show', array('user'=>$user->getUsernameCanonical()));
        }

        return $this->render('PlanningBundle:User:edit_user.html.twig', array(
            'user' => $user,
            'form' => $form->createView()
        ));

    }

    /**
     * @param Group $group
     * @return \Symfony\Component\HttpFoundation\Response
     *
     * @ParamConverter("group", options={"mapping": {"group": "nameCanonical"}})
     */
    function showGroupAction(Group $group) {

        return $this->render('PlanningBundle:User:show_group.html.twig', array(
            'group' => $group
        ));

    }

    /**
     * @param Request $request
     * @param Group $group
     * @param User $user
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @ParamConverter("user", options={"mapping": {"user": "usernameCanonical"}})
     * @ParamConverter("group", options={"mapping": {"group": "name"}})
     */
    function removeUserFromGroupAction(Request $request, Group $group, User $user) {

        $form = $this->createFormBuilder()
            ->add('sure', 'checkbox')
            ->add('remove', 'submit')
            ->getForm()
            ;

        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();

            if ($data['sure']) {

                $em = $this->getDoctrine()->getManager();
                $user->removeGroup($group);
                $em->flush();
            }

            return $this->redirectToRoute('group_show', array('group'=>$group->getNameCanonical()));

        }

        return $this->render('PlanningBundle:User:group_remove_user.html.twig', array(
            'group' => $group, 'user'=> $user, 'form'=>$form->createView()
        ));

    }


    /**
     * @param Request $request
     * @param Group $group
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @ParamConverter("group", options={"mapping": {"group": "nameCanonical"}})
     */
    function addUserToGroupAction(Request $request, Group $group) {
        $em = $this->getDoctrine()->getManager();
        $form = $this->createFormBuilder()
            ->add('users', 'entity', array(
                'class'=>'Insta\PlanningBundle\Entity\User',
                'property' => 'fullname',
                'multiple' => true,
                'expanded' => false
            ))
            ->add('add', 'submit')
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();

            foreach ($data['users'] as $user) {
                $user->addGroup($group);
            }

            $em->flush();



            return $this->redirectToRoute('group_show', array('group'=>$group->getNameCanonical()));

        }

        return $this->render('PlanningBundle:User:group_add_user.html.twig', array(
            'group' => $group, 'form'=>$form->createView()
        ));

    }

    /**
     * @param Request $request
     * @param Group $group
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @ParamConverter("group", options={"mapping": {"group": "nameCanonical"}})
     */
    function editGroupAction(Request $request, Group $group)
    {
        $gm = $this->get('fos_user.group_manager');
        $form = $this->createForm('fos_user_group', $group)
            ->add('add', 'submit');

        $form->handleRequest($request);

        if ($form->isValid()) {
            $gm->updateGroup($group);


            return $this->redirectToRoute('group_show', array('group' => $group->getNameCanonical()));

        }

        return $this->render('PlanningBundle:User:group_edit.html.twig', array(
            'group' => $group, 'form' => $form->createView()
        ));
    }
} 