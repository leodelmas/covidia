<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/planning")
 */
class PlanningController extends AbstractController {

    /**
     * @Route("/", name="planning.index", methods={"GET"})
     * @return Response
     */
    public function index(): Response {
        return $this->render('pages/planning/index.html.twig', []);
    }
}
