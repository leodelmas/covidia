<?php

namespace App\Controller;

use App\Entity\WorkTime;
use App\Form\WorkTimeType;
use App\Repository\WorkTimeRepository;
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
     * @param WorkTimeRepository $workTimeRepository
     * @return Response
     */
    public function index(WorkTimeRepository $workTimeRepository): Response {
        return $this->render('pages/planning/workTime/index.html.twig', [
            'workTimes' => $workTimeRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="workTime.new", methods={"GET","POST"})
     * @param Request $request
     * @param Security $security
     * @return Response
     */
    public function new(Request $request, Security $security): Response {
        $workTime = new WorkTime();
        $workTime->setUser($security->getUser());
        $form = $this->createForm(WorkTimeType::class, $workTime);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($workTime);
            $entityManager->flush();

            return $this->redirectToRoute('planning.index');
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
     * @return Response
     */
    public function edit(Request $request, WorkTime $workTime): Response {
        $form = $this->createForm(WorkTimeType::class, $workTime);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('planning.index');
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
        }

        return $this->redirectToRoute('planning.index');
    }
}
