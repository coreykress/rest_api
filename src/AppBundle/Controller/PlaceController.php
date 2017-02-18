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
     * @Rest\View(serializerGroups={"place"})
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
     * @Rest\View(serializerGroups={"place"})
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
     * @Rest\View(serializerGroups={"place"})
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
     * @Rest\View(statusCode=Response::HTTP_NO_CONTENT, serializerGroups={"place"})
     * @Rest\Delete("/places/{id}")
     */
    public function removePlaceAction(Request $request)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Place $place */
        $place = $this->getDoctrine()
            ->getRepository('AppBundle:Place')
            ->find($request->get('id'));

        if (!$place) {
            return;
        }

        foreach ($place->getPrices() as $price) {
            $em->remove($price);
        }
        $em->remove($place);
        $em->flush();
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
     * @Rest\View(serializerGroups={"place"})
     * @Rest\Put("/places/{id}")
     */
    public function updatePlaceAction(Request $request)
    {
        $this->updatePlace($request, true);
    }

    /**
     * @Rest\View(serializerGroups={"place"})
     * @Rest\Patch("/places/{id}")
     */
    public function patchPlaceAction(Request $request)
    {
        $this->updatePlace($request, false);
    }
}