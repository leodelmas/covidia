<?php

namespace App\Controller;

use App\Entity\Sos;
use App\Form\SosType;
use App\Notification\SosNotification;
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
    public function index(Request $request, Security $security, SosNotification $notification): Response {
        $sos = new Sos();
        $sos->setUser($security->getUser());
        $form = $this->createForm(SosType::class, $sos);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $notification->notify($sos);
            $this->addFlash('success', 'Votre message SOS a bien été envoyé.');
        }

        return $this->render('pages/sos/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
