<?php

namespace AppBundle\Controller;

use AppBundle\Form\UserType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as Rest;
use AppBundle\Entity\User;

class UserController extends Controller
{
    /**
     * @Rest\View()
     * @Rest\Get("/users")
     */
    public function getUsersAction(Request $request)
    {
        $users = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->findAll();

        return $users;
    }

    /**
     * @Rest\View()
     * @Rest\Get("/users/{id}")
     */
    public function getUserAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->find($request->get('id'));

        if (empty($user)) {
            return View::create(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        return $user;
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED)
     * @Rest\Post("/users")
     */
    public function postUsersAction(Request $request)
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);

        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($user);
            $em->flush();
            return $user;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/users/{id}")
     */
    public function removeUsersAction(Request $request)
    {
        /** @var User $user */
        $user = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->find($request->get('id'));

        if ($user) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->remove($user);
            $em->flush();
        }
    }

    private function updateUser(Request $request, $clearMissing)
    {
        $user = $this->getDoctrine()
            ->getRepository('AppBundle:User')
            ->find($request->get('id'));

        if (empty($user)) {
            return View::create(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(UserType::class, $user);
        $form->submit($request->request->all(), $clearMissing);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->merge($user);
            $em->flush();
        } else {
            return $form;
        }
    }

    /**
     * @Rest\View()
     * @Rest\Put("/users/{id}")
     */
    public function updateUsersAction(Request $request)
    {
        $this->updateUser($request, true);
    }

    /**
     * @Rest\View()
     * @Rest\Patch("/users/{id}")
     */
    public function patchUsersAction(Request $request)
    {
        $this->updateUser($request, false);
    }
}