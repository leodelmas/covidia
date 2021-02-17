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
    public function index(): Response
    {
        $this->objectManager = $this->getDoctrine()->getManager();

        return $this->render('pages/stat/index.html.twig', [
            'controller_name' => 'StatController',
            'stat' => $this->req1(),
        ]);
    }

    /**
     * D’avoir le pourcentage du temps de travail en entreprise ou en télétravail, par mois, et par salariés
     */
    public function req1()
    {
        $RAW_QUERY = "
        SELECT SUM(Stat1.nbrTime) AS nbrTime, Stat1.monthYear AS monthYear, Stat1.nameUser as nameUser, Stat1.teleworked as teleworked
        FROM (
                 SELECT SUM(TIMEDIFF(t.date_time_end, t.date_time_start)) AS nbrTime, CONCAT(MONTH(t.date_time_start), '/', YEAR(t.date_time_start)) AS monthYear, CONCAT(u.firstname, ' ', u.lastname) as nameUser, wt.is_teleworked as teleworked
                 FROM task t, user u, work_time wt
                 WHERE t.user_id = u.id
                           AND t.work_time_id = wt.id
                           AND MONTH(t.date_time_start) = MONTH(t.date_time_end)
        
        GROUP BY monthYear, nameUser, teleworked
        
        UNION
        
        SELECT SUM(TIMEDIFF(ADDTIME(LAST_DAY(t.date_time_start), '24:00:00'), t.date_time_start)) AS nbrTime, CONCAT(MONTH(t.date_time_start), '/', YEAR(t.date_time_start)) AS monthYear, CONCAT(u.firstname, ' ', u.lastname) as nameUser, wt.is_teleworked as teleworked
        FROM task t, user u, work_time wt
        WHERE t.user_id = u.id
                  AND t.work_time_id = wt.id
                  AND MONTH(t.date_time_start) <> MONTH(t.date_time_end)
        
        GROUP BY monthYear, nameUser, teleworked
        
        UNION
        
        SELECT SUM(TIMEDIFF(t.date_time_end, ADDTIME(LAST_DAY(t.date_time_start), '24:00:00'))) AS nbrTime, CONCAT(MONTH(t.date_time_end), '/', YEAR(t.date_time_end)) AS monthYear, CONCAT(u.firstname, ' ', u.lastname) as nameUser, wt.is_teleworked as teleworked
        FROM task t, user u, work_time wt
        WHERE t.user_id = u.id
                  AND t.work_time_id = wt.id
                  AND MONTH(t.date_time_start) <> MONTH(t.date_time_end)
        
        GROUP BY monthYear, nameUser, teleworked
            ) AS Stat1
        
        GROUP BY monthYear, nameUser, teleworked
        ORDER BY monthYear, nameUser, teleworked";

        $statement = $this->objectManager->getConnection()->prepare($RAW_QUERY);
        $statement->execute();

        $result = $statement->fetchAll();

        for($i=0; $i < count($result); $i++){
            if($result[$i]['teleworked'] == 0){
                $tabPourcent[$result[$i]['monthYear']][$result[$i]['nameUser']] = intval($result[$i]['nbrTime']);

                for($l=0; $l < count($result); $l++){
                    if($result[$i]['monthYear'] == $result[$l]['monthYear']
                        && $result[$i]['nameUser'] == $result[$l]['nameUser']
                        && $result[$i]['teleworked'] != $result[$l]['teleworked']){

                        $tabPourcent[$result[$i]['monthYear']][$result[$i]['nameUser']] =
                            100* (intval($result[$i]['nbrTime']) / ( intval($result[$i]['nbrTime']) + intval($result[$l]['nbrTime'])) );
                    }
                }

                if($tabPourcent[$result[$i]['monthYear']][$result[$i]['nameUser']] > 100){
                    $tabPourcent[$result[$i]['monthYear']][$result[$i]['nameUser']] = 100;
                }
            }else
            {

            }
        }

        dump($tabPourcent);

        $tab = [
            'type' => 'bar',
            'data' => [
                'labels' => ['Sylvain'],
                'datasets' => [
                    'label' => ['dsds'],
                    'data' => [50],
                    'backgroundColor' => ['rgba(255, 99, 132, 0.2)','rgba(54, 162, 235, 0.2)'],
                    'borderColor' => ['rgba(255, 99, 132, 1)','rgba(54, 162, 235, 1)',],
                    'borderWidth' => 1
                ]
            ],
            'options' => [
                'title' => [
                    'display' => true,
                    'text' => 'Pourcentage du temps de travail en télétravail, sur le mois de 02/2021, par salariés'
                ]
            ]
        ];
        //return json_encode($result);
        return json_encode($tab);
    }
}
