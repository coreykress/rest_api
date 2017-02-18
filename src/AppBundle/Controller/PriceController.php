<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Place;
use AppBundle\Entity\Price;
use AppBundle\Form\PriceType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use FOS\RestBundle\View\View;
use FOS\RestBundle\Controller\Annotations as Rest;

class PriceController extends Controller
{
    /**
     * @Rest\View(serializerGroups={"price"})
     * @Rest\Get("/places/{id}/prices")
     */
    public function getPricesAction(Request $request)
    {
        /** @var Place $place */
        $place= $this->getDoctrine()
            ->getRepository('AppBundle:Place')
            ->find($request->get('id'));

        if (empty($place)) {
            return $this->placeNotFound();
        }

        return $place->getPrices();
    }

    /**
     * @Rest\View(statusCode=Response::HTTP_CREATED, serializerGroups={"price"})
     * @Rest\Post("/places/{id}/prices")
     */
    public function postPricesAction(Request $request)
    {
        /** @var Place $place */
        $place= $this->getDoctrine()
            ->getRepository('AppBundle:Place')
            ->find($request->get('id'));

        if (empty($place)) {
            return $this->placeNotFound();
        }

        $price = new Price();
        $price->setPlace($place);
        $form = $this->createForm(PriceType::class, $price);

        $form->submit($request->request->all());

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($price);
            $em->flush();

            return $price;
        } else {
            return $form;
        }
    }

    private function placeNotFound()
    {
        return View::create(['message' => 'Place not found'], Response::HTTP_NOT_FOUND);
    }
}