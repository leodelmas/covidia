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
 * @Route("/task/category")
 */
class TaskCategoryController extends AbstractController
{
    /**
     * @Route("/", name="task_category_index", methods={"GET"})
     */
    public function index(TaskCategoryRepository $taskCategoryRepository): Response
    {
        return $this->render('task_category/index.html.twig', [
            'task_categories' => $taskCategoryRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="task_category_new", methods={"GET","POST"})
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

            return $this->redirectToRoute('task_category_index');
        }

        return $this->render('task_category/new.html.twig', [
            'task_category' => $taskCategory,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="task_category_show", methods={"GET"})
     */
    public function show(TaskCategory $taskCategory): Response
    {
        return $this->render('task_category/show.html.twig', [
            'task_category' => $taskCategory,
        ]);
    }

    /**
     * @Route("/{id}/edit", name="task_category_edit", methods={"GET","POST"})
     */
    public function edit(Request $request, TaskCategory $taskCategory): Response
    {
        $form = $this->createForm(TaskCategoryType::class, $taskCategory);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('task_category_index');
        }

        return $this->render('task_category/edit.html.twig', [
            'task_category' => $taskCategory,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="task_category_delete", methods={"DELETE"})
     */
    public function delete(Request $request, TaskCategory $taskCategory): Response
    {
        if ($this->isCsrfTokenValid('delete'.$taskCategory->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($taskCategory);
            $entityManager->flush();
        }

        return $this->redirectToRoute('task_category_index');
    }
}
