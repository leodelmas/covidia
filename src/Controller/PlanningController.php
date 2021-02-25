<?php

namespace App\Controller;

use App\Entity\PlanningSearch;
use App\Form\PlanningSearchType;
use App\Repository\TaskCategoryRepository;
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
     * @param TaskCategoryRepository $taskCategoryRepository
     * @return Response
     */
    public function index(Request $request, TaskCategoryRepository $taskCategoryRepository): Response {
        return $this->render('pages/planning/index.html.twig', [
            'taskCategories' => $taskCategoryRepository->findAll()
        ]);
    }
}
