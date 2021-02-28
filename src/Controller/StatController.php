<?php

namespace App\Controller;

use App\Repository\StatRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;

class StatController extends AbstractController
{
    /**
     * ObjectManager
     */
    private $objectManager;

    /**
     * @Route("/stat", name="stat", methods={"GET"})
     */
    public function index(Request $request, StatRepository $stats): Response
    {
        $stats->setObjectManager($this->getDoctrine()->getManager());

        if(null == $request->query->get('month')){
            $month = date("n/Y");
        }else{
            $month = $request->query->get('month');
        }

        $time = strtotime(str_replace('/', '-', '01/'.$month));
        $nextMonth = date("n/Y", strtotime("+1 month", $time));
        $previousMonth = date("n/Y", strtotime("-1 month", $time));

        return $this->render('pages/stat/index.html.twig', [
            'controller_name' => 'StatController',
            'month' => $month,
            'nextMonth' => $nextMonth,
            'previousMonth' => $previousMonth,
            'stat1' => $stats->req1($month),
            'stat2' => $stats->req2($month),
            'stat5' => $stats->req5($month),
        ]);
    }
}
