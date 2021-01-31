<?php

namespace App\Controller;

use App\Entity\Sos;
use App\Form\SosType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

class SosController extends AbstractController
{
    /**
     * @Route("/sos", name="sos.index")
     */
    public function index(Request $request, Security $security): Response
    {
        $sos = new Sos();
        $sos->setEmail(0);
        $sos->setUser($security->getUser());
        $form = $this->createForm(SosType::class, $sos);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

            $this->addFlash('success', 'Votre email a bien été envoyé');
        }

        return $this->render('pages/sos/index.html.twig', [
            'form' => $form->createView()
        ]);
    }
}
