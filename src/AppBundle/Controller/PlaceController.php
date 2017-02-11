<?php

namespace AppBundle\Controller;

use AppBundle\Form\PlaceType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\Controller\Annotations as Rest;
use FOS\RestBundle\View\View;
use AppBundle\Entity\Place;

class PlaceController extends Controller
{

    /**
     * @Rest\View()
     * @Rest\Get("/places")
     */
    public function getPlacesAction(Request $request)
    {
        $places = $this->getDoctrine()
            ->getRepository('AppBundle:Place')
            ->findAll();

        return  $places;
    }


    /**
     * @Rest\View()
     * @Rest\Get("/places/{id}")
     */
    public function getPlaceAction(Request $request)
    {
        /** @var Place $place */
        $place = $this->getDoctrine()
            ->getRepository('AppBundle:Place')
            ->find($request->get('id'));

        if (empty($place)) {
            return View::create(['message' => 'Place not found'], Response::HTTP_NOT_FOUND);
        }

        return $place;
    }

    /**
     * @Rest\View()
     * @Rest\Post("/places")
     */
    public function postPlacesAction(Request $request)
    {
        $place = new Place();

        $form = $this->createForm(PlaceType::class, $place);
        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($place);
            $em->flush();

            return $place;
        } else {
            return $form;
        }
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT)
     * @Rest\Delete("/places/{id}")
     */
    public function removePlaceAction(Request $request)
    {
        /** @var Place $place */
        $place = $this->getDoctrine()
            ->getRepository('AppBundle:Place')
            ->find($request->get('id'));

        if ($place) {
            $em = $this->getDoctrine()->getManager();
            $em->remove($place);
            $em->flush();
        }
    }

    private function UpdatePlace(Request $request, $clearMissing)
    {
        $place = $this->getDoctrine()
            ->getRepository('AppBundle:Place')
            ->find($request->get('id'));

        if (empty($place)) {
            return View::create(['message' => 'Place not found'], Response::HTTP_NOT_FOUND);
        }

        $form = $this->createForm(PlaceType::class, $place);
        $form->submit($request->request->all(), $clearMissing);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->merge($place);
            $em->flush();
        }else {
            return $form;
        }
    }

    /**
     * @Rest\View()
     * @Rest\Put("/places/{id}")
     */
    public function updatePlaceAction(Request $request)
    {
        $this->updatePlace($request, true);
    }

    /**
     * @Rest\View()
     * @Rest\Patch("/places/{id}")
     */
    public function patchPlaceAction(Request $request)
    {
        $this->updatePlace($request, false);
    }
}