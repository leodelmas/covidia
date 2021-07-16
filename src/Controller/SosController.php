<?php

namespace App\Controller;

use App\Entity\Sos;
use App\Entity\User;
use App\Form\SosType;
use App\Notification\SosNotification;
use App\Repository\SosRepository;
use App\Repository\UserRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class SosController extends AbstractController {

    /**
     * @Route("/sos", name="sos.index")
     * @param Request $request
     * @param Security $security
     * @param SosNotification $notification
     * @return Response
     */
    public function index(Request $request, Security $security, SosNotification $notification,UserRepository $userRepository): Response {
        $sos = new Sos();
        $sos->setUser($security->getUser());
        $form = $this->createForm(SosType::class, $sos);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $psychologists = $userRepository->findBy(['isPsychologist' => 1]);
            $notification->notify($sos,$psychologists);
            $this->addFlash('success', 'Votre message SOS a bien été envoyé.');
            $entityManager = $this->getDoctrine()->getManager();
            $entityManager->persist($sos);
            $entityManager->flush();
            return $this->redirectToRoute('sos.index');
        }

        return $this->render('pages/sos/index.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/sos/list", name="sos.list")
     * @param Security $security
     * @param PaginatorInterface $paginator
     * @param Request $request
     * @param SosRepository $sosRepository
     * @return Response
     */
    public function list(Security $security, PaginatorInterface $paginator, Request $request, SosRepository $sosRepository): Response
    {
        if(false == $security->getUser()->getIsPsychologist())
        {
            return $this->redirectToRoute('planning.index');
        }
        
        $sosRequests = $paginator->paginate(
            $sosRepository->findAll(),
            $request->query->getInt('page', 1),
            20
        );

        return $this->render('pages/sos/list.html.twig', [
            'sosRequests' => $sosRequests
        ]);
    }

    /**
     * @Route("/sos/{id}", name="sos.close", methods={"CLOSE"})
     * @param Request $request
     * @param Sos $sos
     * @return Response
     */
    public function close(Request $request, Sos $sos): Response {
        if ($this->isCsrfTokenValid('close'.$sos->getId(), $request->request->get('_token'))) {
            $sos->setIsClosed(true);
            $this->getDoctrine()->getManager()->flush();
            $this->addFlash('success', 'SOS fermé avec succès !');
        }

        return $this->redirectToRoute('sos.list');
    }
}
