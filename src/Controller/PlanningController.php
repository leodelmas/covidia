<?php

namespace App\Controller;

use App\Entity\PlanningSearch;
use App\Form\PlanningSearchType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/planning")
 */
class PlanningController extends AbstractController {

    /**
     * @Route("/", name="planning.index", methods={"GET"})
     * @param Request $request
     * @return Response
     */
    public function index(Request $request): Response {
        $search = new PlanningSearch();
        $form   = $this->createForm(PlanningSearchType::class, $search);
        $form->handleRequest($request);

        return $this->render('pages/planning/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
