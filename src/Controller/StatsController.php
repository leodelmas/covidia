<?php

namespace App\Controller;

use App\Repository\StatsRepository;
use App\Repository\TaskCategoryRepository;
use App\Repository\UserRepository;
use Doctrine\Persistence\ObjectManager;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;

class StatsController extends AbstractController
{
    /**
     * ObjectManager
     */
    private $objectManager;

    /**
     * @Route("/stats", name="stats.index", methods={"GET"})
     * @param Request $request
     * @param StatsRepository $stats
     * @param TaskCategoryRepository $taskCategoryRepository
     * @return Response
     */
    public function index(Request $request, StatsRepository $stats, TaskCategoryRepository $taskCategoryRepository): Response {
        $stats->setObjectManager($this->getDoctrine()->getManager());

        if(null == $request->query->get('month')){
            $month = date("n/Y");
        }else{
            $month = $request->query->get('month');
        }

        $tasksCategory = $taskCategoryRepository->findAll();

        $time = strtotime(str_replace('/', '-', '01/'.$month));
        $nextMonth = date("n/Y", strtotime("+1 month", $time));
        $previousMonth = date("n/Y", strtotime("-1 month", $time));

        return $this->render('pages/stats/index.html.twig', [
            'controller_name' => 'StatsController',
            'month' => $month,
            'nextMonth' => $nextMonth,
            'previousMonth' => $previousMonth,
            'stat1' => $stats->req1($month),
            'stat2' => $stats->req2($month),
            'stat3' => $stats->req3($month, $tasksCategory),
            'stat5' => $stats->req5($month),
            'tcategs' => $tasksCategory, //Use with Stat3
        ]);
    }
}
