<?php

namespace AppBundle\Controller;

use AppBundle\Entity\User;
use AppBundle\Form\PreferenceType;
use FOS\RestBundle\View\View;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use AppBundle\Entity\Preference;

class PreferenceController extends Controller
{

    /**
     * @Rest\View(serializerGroups={"preference"})
     * @Rest\Get("/users/{id}/preferences")
     */
    public function getPreferencesAction(Request $request)
    {
        /* @var $user User */
        $user = $this->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:User')
            ->find($request->get('id'));

        if (empty($user)) {
            return $this->userNotFound();
        }

        return $user->getPreferences();
    }


    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"preference"})
     * @Rest\Post("/users/{id}/preferences")
     */
    public function postPreferencesAction(Request $request)
    {
        /* @var $user User */
        $user = $this->get('doctrine.orm.entity_manager')
            ->getRepository('AppBundle:User')
            ->find($request->get('id'));

        if (empty($user)) {
            return $this->userNotFound();
        }

        $preference = new Preference();
        $preference->setUser($user);
        $form = $this->createForm(PreferenceType::class, $preference);

        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em = $this->get('doctrine.orm.entity_manager');
            $em->persist($preference);
            $em->flush();
            return $preference;
        } else {
            return $form;
        }
    }

    private function userNotFound()
    {
        return View::create(['message' => 'User not found'], Response::HTTP_NOT_FOUND);
    }
}