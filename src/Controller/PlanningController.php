<?php

namespace App\Controller;

use App\Repository\TaskCategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PlanningController extends AbstractController {

    /**
     * @Route("/", name="planning.index", methods={"GET"})
     * @param TaskCategoryRepository $taskCategoryRepository
     * @return Response
     */
    public function index(TaskCategoryRepository $taskCategoryRepository): Response {
        return $this->render('pages/planning/index.html.twig', [
            'taskCategories' => $taskCategoryRepository->findAll()
        ]);
    }
}
