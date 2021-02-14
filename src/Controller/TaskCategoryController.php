<?php

namespace App\Controller;

use App\Entity\TaskCategory;
use App\Form\TaskCategoryType;
use App\Repository\TaskCategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/task_category")
 */
class TaskCategoryController extends AbstractController
{
    /**
     * @Route("/", name="task_category.index", methods={"GET"})
     * @param TaskCategoryRepository $taskCategoryRepository
     * @return Response
     */
    public function index(TaskCategoryRepository $taskCategoryRepository): Response
    {
        return $this->render('pages/task_category/index.html.twig', [
            'task_categories' => $taskCategoryRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="task_category.new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $taskCategory = new TaskCategory();
        $form = $this->createForm(TaskCategoryType::class, $taskCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($taskCategory);
            $entityManager->flush();

            return $this->redirectToRoute('task_category.index');
        }

        return $this->render('pages/task_category/new.html.twig', [
            'task_category' => $taskCategory,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="task_category.edit", methods={"GET","POST"})
     * @param Request $request
     * @param TaskCategory $taskCategory
     * @return Response
     */
    public function edit(Request $request, TaskCategory $taskCategory): Response
    {
        $form = $this->createForm(TaskCategoryType::class, $taskCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('task_category.index');
        }

        return $this->render('pages/task_category/edit.html.twig', [
            'task_category' => $taskCategory,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="task_category.delete", methods={"DELETE"})
     * @param Request $request
     * @param TaskCategory $taskCategory
     * @return Response
     */
    public function delete(Request $request, TaskCategory $taskCategory): Response
    {
        if ($this->isCsrfTokenValid('delete'.$taskCategory->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($taskCategory);
            $entityManager->flush();
        }

        return $this->redirectToRoute('task_category.index');
    }
}
