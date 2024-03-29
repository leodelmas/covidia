<?php

namespace App\Controller;

use App\Entity\Task;
use App\Form\TaskType;
use App\Repository\TaskRepository;
use App\Repository\WorkTimeRepository;
use Doctrine\ORM\NonUniqueResultException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Knp\Component\Pager\PaginatorInterface;

/**
 * @Route("/planning/task")
 */
class TaskController extends AbstractController
{
    /**
     * @Route("/", name="task.index", methods={"GET"})
     * @param PaginatorInterface $paginator
     * @param TaskRepository $taskRepository
     * @param Security $security
     * @param Request $request
     * @return Response
     */
    public function index(PaginatorInterface $paginator, TaskRepository $taskRepository, Security $security, Request $request): Response
    {
        $tasks = $paginator->paginate(
            $taskRepository->findAllByUser($security->getUser()->getId()),
            $request->query->getInt('page', 1),
            20
        );

        return $this->render('pages/planning/task/index.html.twig', [
            'tasks' => $tasks,
        ]);
    }

    /**
     * @Route("/new", name="task.new", methods={"GET","POST"})
     * @param Request $request
     * @param Security $security
     * @param WorkTimeRepository $workTimeRepository
     * @return Response
     * @throws NonUniqueResultException
     */
    public function new(Request $request, Security $security, WorkTimeRepository $workTimeRepository): Response {
        $task = new Task();
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $user = $security->getUser();
            $workTime = $workTimeRepository->findRightWorkTimeForTask($user->getId(), $task->getDateTimeStart(), $task->getDateTimeEnd());
            if($workTime == null) {
                $this->addFlash('danger', 'La tâche n\'est pas planifiée dans une période valide.');
                return $this->redirectToRoute('task.index');
            }
            $task->setWorkTime($workTime);
            if($task->getTaskCategory()->getIsPhysical() && $task->getTaskCategory()->getIsRemote() || $task->getTaskCategory()->getIsRemote() && $workTime->getIsTeleworked() || $task->getTaskCategory()->getIsPhysical() && !$workTime->getIsTeleworked()) {
                $task->setUser($user);
                $entityManager->persist($task);
                $entityManager->flush();
                $this->addFlash('success', 'Tâche créée avec succès !');
                return $this->redirectToRoute('task.index');
            }
            else {
                $this->addFlash('danger', 'La catégorie de tâche est invalide pour la période séléctionnée.');
                return $this->redirectToRoute('task.index');
            }
        }

        return $this->render('pages/planning/task/new.html.twig', [
            'task' => $task,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="task.edit", methods={"GET","POST"})
     * @param Request $request
     * @param Task $task
     * @param Security $security
     * @param WorkTimeRepository $workTimeRepository
     * @return Response
     * @throws NonUniqueResultException
     */
    public function edit(Request $request, Task $task, Security $security, WorkTimeRepository $workTimeRepository): Response {
        $form = $this->createForm(TaskType::class, $task);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $security->getUser();
            $workTime = $workTimeRepository->findRightWorkTimeForTask($user->getId(), $task->getDateTimeStart(), $task->getDateTimeEnd());
            if($workTime == null) {
                $this->addFlash('danger', 'La tâche n\'est pas planifiée dans une période valide.');
                return $this->redirectToRoute('task.index');
            }
            $task->setWorkTime($workTime);
            if($task->getTaskCategory()->getIsPhysical() && $task->getTaskCategory()->getIsRemote() || $task->getTaskCategory()->getIsRemote() && $workTime->getIsTeleworked() || $task->getTaskCategory()->getIsPhysical() && !$workTime->getIsTeleworked()) {
                $this->getDoctrine()->getManager()->flush();
                $this->addFlash('success', 'Tâche modifiée avec succès !');
                return $this->redirectToRoute('task.index');
            }
            else {
                $this->addFlash('danger', 'La catégorie de tâche est invalide pour la période séléctionnée.');
                return $this->redirectToRoute('task.index');
            }
        }

        return $this->render('pages/planning/task/edit.html.twig', [
            'task' => $task,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="task.delete", methods={"DELETE"})
     * @param Request $request
     * @param Task $task
     * @return Response
     */
    public function delete(Request $request, Task $task): Response {
        if ($this->isCsrfTokenValid('delete'.$task->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($task);
            $entityManager->flush();
            $this->addFlash('success', 'Tâche supprimée avec succès !');
        }
        return $this->redirectToRoute('task.index');
    }
}
