<?php
/**
 * Created by PhpStorm.
 * User: Rodolphe
 * Date: 07/12/2014
 * Time: 18:05
 */

namespace Insta\PlanningBundle\Controller;


use Doctrine\DBAL\Connection;
use Insta\PlanningBundle\Entity\Group;
use Insta\PlanningBundle\Entity\Student;
use Insta\PlanningBundle\Entity\Teacher;
use Insta\PlanningBundle\Entity\Tutor;
use Insta\PlanningBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\FormError;
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

        return $this->render('PlanningBundle:User:_index.html.twig', array(
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

    function groupListAction($offset) {
        $repo = $this->getDoctrine()->getRepository('PlanningBundle:Group');
        $nbPage = (int) floor(count($repo->findAll()) /20);
        $groups = $repo->findBy(array(),null, 20, $offset * 20);
        return $this->render('PlanningBundle:User:group_list.html.twig', array(
            'groups'=>$groups,
            'nbPages' => $nbPage,
            'offset' => $offset
        ));
    }

    function addUserAction(Request $request, $type) {
        $um = $this->get('fos_user.user_manager');

        $form = $this->createFormBuilder()
            ->add('firstname', 'text', array('label'=>'user.firstname'))
            ->add('lastname', 'text', array('label'=>'user.lastname'))
            ->add('username', 'text', array('label'=>'user.username'))
            ->add('email', 'email', array('label'=>'user.email'))
            ->add('type', 'choice', array(
                'label'=>'user.type',
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
            ->add('password', 'text', array('label'=>'user.password', 'read_only'=>true))
            ->add('add', 'submit', array('label'=>'form.add'))

            ->getForm()
        ;

        $form->get('password')->setData(uniqid());

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
            $user->setPassword($data['password']);
            $user->setLastname($data['lastname']);
            $user->setEmail($data['email']);
            $user->setUsername($data['username']);

            $um->updateUser($user);
            return $this->redirectToRoute('user_group_overview');
        }


        return $this->render('PlanningBundle:User:user_add.html.twig', array(
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
            ->add('add', 'submit', array('form.add'));

        $form->handleRequest($request);

        if ($form->isValid()) {
            $gm->updateGroup($group);

            return $this->redirectToRoute('user_group_overview');
        }

        return $this->render('PlanningBundle:User:group_add.html.twig', array(
            'form' => $form->createView()
        ));
    }

    /**
     * @ParamConverter("user", options={"mapping": {"user": "usernameCanonical"}})
     * @param User $user
     * @return \Symfony\Component\HttpFoundation\Response
     */
    function showUserAction(User $user) {

        return $this->render('PlanningBundle:User:user_show.html.twig', array(
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
            ->add('username', 'text', array('label'=>'user.username'))
            ->add('firstname', 'text', array('label'=>'user.firstname'))
            ->add('lastname', 'text', array('label'=>'user.lastname'))
            ->add('email', 'email', array('label'=>'user.email'))
            ->add('groups', 'entity', array(
                'class' => 'Insta\PlanningBundle\Entity\Group',
                'property' => 'name',
                'multiple' => true,
                'expanded' => true,
                'label' => 'user.groups'
            ))
            ->add('locked', 'checkbox', array('required'=>false, 'label'=>'user.locked'))
            ->add('edit', 'submit', array('label'=>'form.edit'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $um->updateUser($user);

            return $this->redirectToRoute('user_show', array('user'=>$user->getUsernameCanonical()));
        }

        return $this->render('PlanningBundle:User:user_edit.html.twig', array(
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

        return $this->render('PlanningBundle:User:group_show.html.twig', array(
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
            ->add('sure', 'checkbox', array('label'=>'form.sure'))
            ->add('remove', 'submit', array('label'=>'form.remove'))
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
                'expanded' => false,
                'label' => 'group.users'
            ))
            ->add('add', 'submit', array('form.add'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();

            foreach ($data['users'] as $user) {
                /** @var User $user */
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
            ->add('add', 'submit', array('label'=>'form.add'));

        $form->handleRequest($request);

        if ($form->isValid()) {
            $gm->updateGroup($group);


            return $this->redirectToRoute('group_show', array('group' => $group->getNameCanonical()));

        }

        return $this->render('PlanningBundle:User:group_edit.html.twig', array(
            'group' => $group, 'form' => $form->createView()
        ));
    }

    /**
     * @param Request $request
     * @param Group $group
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @ParamConverter("group", options={"mapping": {"group": "nameCanonical"}})
     */
    function deleteGroupAction(Request $request, Group $group) {

        $form = $this->createFormBuilder()
            ->add('sure', 'checkbox', array('label'=>'form.sure'))
            ->add('remove', 'submit', array('label'=>'form.remove'))
            ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();

            if ($data['sure']) {

                $em = $this->getDoctrine()->getManager();
                $em->remove($group);
                $em->flush();
            }

            return $this->redirectToRoute('user_group_overview');

        }

        return $this->render('PlanningBundle:User:group_delete.html.twig', array(
            'group' => $group, 'form'=>$form->createView()
        ));
    }

    /**
     * @param Request $request
     * @param User $user
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     * @ParamConverter("user", options={"mapping": {"user": "usernameCanonical"}})
     */
    function deleteUserAction(Request $request, User $user) {

        $form = $this->createFormBuilder()
            ->add('sure', 'checkbox', array('label'=>'form.sure'))
            ->add('remove', 'submit', array('label'=>'form.remove'))
            ->getForm()
        ;

        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();

            if ($data['sure']) {

                $em = $this->getDoctrine()->getManager();
                $em->remove($user);
                $em->flush();
            }

            return $this->redirectToRoute('user_group_overview');

        }

        return $this->render('PlanningBundle:User:user_delete.html.twig', array(
            'user' => $user, 'form'=>$form->createView()
        ));
    }

    function switchUserTypeAction(Request $request) {

        $em = $this->getDoctrine()->getManager();

        $form = $this->createFormBuilder()
            ->add('user', 'entity', array(
                'class' => 'Insta\PlanningBundle\Entity\User',
                'property' => 'fullname',
                'group_by' => 'type',
                'label'=>'user.user'
            ))
            ->add('type', 'choice', array(
                'choices' => array(
                    self::TYPE_USER => 'Aucun type spécifique / Administration',
                    self::TYPE_STUDENT => 'Elève',
                    self::TYPE_TUTOR => 'Tuteur',
                    self::TYPE_TEACHER => 'Professeur'),
                'expanded' => true,
                'multiple' => false,
                'label'=>'user.type'
            ))
            ->add('edit', 'submit', array('label'=>'form.edit'))
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {

            /** @var User $user */
            $user = $form->get('user')->getData();

            $type = $form->get('type')->getData();

            try {
                $this->get('planning.users')->switchUserType($user, $type);
                return $this->redirectToRoute('user_show', array('user'=> $user->getUsernameCanonical()));
            } catch (\Exception $e) {
                $form->addError(new FormError($e->getMessage()));
            }


        }


        return $this->render('PlanningBundle:User:user_switch_type.html.twig', array(
            'form'=> $form->createView()
        ));

    }
} 