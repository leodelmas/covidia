<?php

namespace App\Controller;

use App\Repository\StatRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StatController extends AbstractController
{
    /**
     * ObjectManager
     */
    private $objectManager;

    /**
     * @Route("/stat", name="stat")
     */
    public function index(StatRepository $stats): Response
    {
        $stats->setObjectManager($this->getDoctrine()->getManager());

        return $this->render('pages/stat/index.html.twig', [
            'controller_name' => 'StatController',
            'stat1' => $stats->req1(),
            'stat2' => $stats->req2(),
        ]);
    }
}
