<?php

namespace App\Controller;

use App\Entity\Job;
use App\Form\JobType;
use App\Repository\JobRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/admin/job")
 */
class JobController extends AbstractController
{
    /**
     * @Route("/", name="job.index", methods={"GET"})
     * @param JobRepository $jobRepository
     * @return Response
     */
    public function index(PaginatorInterface $paginator, JobRepository $jobRepository, Request $request): Response
    {
        $jobs = $paginator->paginate(
            $jobRepository->findAll(),
            $request->query->getInt('page', 1),
            20
        );

        return $this->render('pages/job/index.html.twig', [
            'jobs' => $jobs,
        ]);
    }

    /**
     * @Route("/new", name="job.new", methods={"GET","POST"})
     * @param Request $request
     * @return Response
     */
    public function new(Request $request): Response
    {
        $job = new Job();
        $form = $this->createForm(JobType::class, $job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($job);
            $entityManager->flush();
            $this->addFlash('success', 'Emploi créé avec succès !');
            return $this->redirectToRoute('job.index');
        }

        return $this->render('pages/job/new.html.twig', [
            'job' => $job,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}/edit", name="job.edit", methods={"GET","POST"})
     * @param Request $request
     * @param Job $job
     * @return Response
     */
    public function edit(Request $request, Job $job): Response
    {
        $form = $this->createForm(JobType::class, $job);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'Emploi modifié avec succès !');
            return $this->redirectToRoute('job.index');
        }

        return $this->render('pages/job/edit.html.twig', [
            'job' => $job,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/{id}", name="job.delete", methods={"DELETE"})
     * @param Request $request
     * @param Job $job
     * @return Response
     */
    public function delete(Request $request, Job $job): Response
    {
        if ($this->isCsrfTokenValid('delete'.$job->getId(), $request->request->get('_token'))) {
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->remove($job);
            $entityManager->flush();
            $this->addFlash('success', 'Emploi supprimé avec succès !');
        }

        return $this->redirectToRoute('job.index');
    }
}
