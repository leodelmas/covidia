<?php

namespace App\Controller;

use App\Entity\WorkTime;
use App\Form\WorkTimeType;
use App\Repository\WorkTimeRepository;
use Doctrine\ORM\NonUniqueResultException;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * @Route("/planning/workTime")
 */
class WorkTimeController extends AbstractController {

    /**
     * @Route("/", name="workTime.index", methods={"GET"})
     * @param PaginatorInterface $paginator
     * @param WorkTimeRepository $workTimeRepository
     * @param Security $security
     * @param Request $request
     * @return Response
     */
    public function index(PaginatorInterface $paginator, WorkTimeRepository $workTimeRepository, Security $security, Request $request): Response
    {
        $workTimes = $paginator->paginate(
            $workTimeRepository->findAllByUser($security->getUser()->getId()),
            $request->query->getInt('page', 1),
            20
        );

        return $this->render('pages/planning/workTime/index.html.twig', [
            'workTimes' => $workTimes,
        ]);
    }

    /**
     * @Route("/new", name="workTime.new", methods={"GET","POST"})
     * @param Request $request
     * @param Security $security
     * @param WorkTimeRepository $workTimeRepository
     * @return Response
     * @throws NonUniqueResultException
     */
    public function new(Request $request, Security $security, WorkTimeRepository $workTimeRepository): Response {
        $workTime = new WorkTime();
        $form = $this->createForm(WorkTimeType::class, $workTime);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user = $security->getUser();
            $workTimeAlreadyExist = $workTimeRepository->findAlreadyPlannedWorkTime($user->getId(), $workTime->getDateStart(), $workTime->getDateEnd());
            if($workTimeAlreadyExist !== null) {
                $this->addFlash('danger', 'Impossible de superposer deux périodes.');
                return $this->redirectToRoute('workTime.index');
            }
            else {
                $workTime->setUser($user);
                $entityManager = $this->getDoctrine()->getManager();
                $entityManager->persist($workTime);
                $entityManager->flush();
                $this->addFlash('success', 'Période créée avec succès !');
            }
            return $this->redirectToRoute('workTime.index');
        }

        return $this->render('pages/planning/workTime/new.html.twig', [
            'workTime' => $workTime,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="workTime.edit", methods={"GET","POST"})
     * @param Request $request
     * @param WorkTime $workTime
     * @param Security $security
     * @param WorkTimeRepository $workTimeRepository
     * @return Response
     * @throws NonUniqueResultException
     */
    public function edit(Request $request, WorkTime $workTime, Security $security, WorkTimeRepository $workTimeRepository): Response {
        $form = $this->createForm(WorkTimeType::class, $workTime);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $user = $security->getUser();
            $workTimeAlreadyExist = $workTimeRepository->findAlreadyPlannedWorkTime($user->getId(), $workTime->getDateStart(), $workTime->getDateEnd());
            if($workTimeAlreadyExist !== null) {
                $this->addFlash('danger', 'Impossible de superposer deux périodes.');
                return $this->redirectToRoute('workTime.index');
            }
            $this->addFlash('success', 'Période modifiée avec succès !');
            return $this->redirectToRoute('workTime.index');
        }

        return $this->render('pages/planning/workTime/edit.html.twig', [
            'workTime' => $workTime,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="workTime.delete", methods={"DELETE"})
     * @param Request $request
     * @param WorkTime $workTime
     * @return Response
     */
    public function delete(Request $request, WorkTime $workTime): Response {
        if ($this->isCsrfTokenValid('delete'.$workTime->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($workTime);
            $entityManager->flush();
            $this->addFlash('success', 'Période supprimée avec succès !');
        }

        return $this->redirectToRoute('workTime.index');
    }
}
