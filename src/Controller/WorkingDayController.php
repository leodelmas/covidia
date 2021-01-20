<?php

namespace App\Controller;

use App\Entity\WorkingDay;
use App\Form\WorkingDayType;
use App\Repository\WorkingDayRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/planning")
 */
class WorkingDayController extends AbstractController
{
    /**
     * @Route("/", name="working_day.index", methods={"GET"})
     * @param WorkingDayRepository $workingDayRepository
     * @return Response
     */
    public function index(WorkingDayRepository $workingDayRepository): Response
    {
        return $this->render('pages/working_day/index.html.twig', [
            'working_days' => $workingDayRepository->findAll(),
        ]);
    }

    /**
     * @Route("/new", name="working_day.new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $workingDay = new WorkingDay();
        $form = $this->createForm(WorkingDayType::class, $workingDay);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($workingDay);
            $entityManager->flush();

            return $this->redirectToRoute('working_day.index');
        }

        return $this->render('pages/working_day/new.html.twig', [
            'working_day' => $workingDay,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="working_day.edit", methods={"GET","POST"})
     * @param Request $request
     * @param WorkingDay $workingDay
     * @return Response
     */
    public function edit(Request $request, WorkingDay $workingDay): Response
    {
        $form = $this->createForm(WorkingDayType::class, $workingDay);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();

            return $this->redirectToRoute('working_day.index');
        }

        return $this->render('pages/working_day/edit.html.twig', [
            'working_day' => $workingDay,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="working_day.delete", methods={"DELETE"})
     * @param Request $request
     * @param WorkingDay $workingDay
     * @return Response
     */
    public function delete(Request $request, WorkingDay $workingDay): Response
    {
        if ($this->isCsrfTokenValid('delete'.$workingDay->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($workingDay);
            $entityManager->flush();
        }

        return $this->redirectToRoute('working_day.index');
    }
}
