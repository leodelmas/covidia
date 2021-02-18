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
        HAVING monthYear = '2/2021'
        ORDER BY monthYear, nameUser, teleworked";

        $statement = $this->objectManager->getConnection()->prepare($RAW_QUERY);
        $statement->execute();

        $result = $statement->fetchAll();

        //Filtrage pour un traitement plus simple
        for($i=0; $i < count($result); $i++){
            $filterTab[$result[$i]['nameUser']][$result[$i]['teleworked']] = intval($result[$i]['nbrTime']);
        }

        $labeltab = array();
        $datatab = array();

        //Calcul pourcentage
        foreach($filterTab as $clef => $valeur){
            dump($valeur[0], $valeur[1]);
            //Pas de présentiel
            if(false == isset($valeur[0])){
                array_push($labeltab, $clef);
                array_push($datatab, 100);
                //Pas de télétravail
            }else if(false == isset($valeur[1])){
                array_push($labeltab, $clef);
                array_push($datatab, 0);
                //Les deux
            }else{
                array_push($labeltab, $clef);
                array_push($datatab, round(100 * (intval($valeur[1]) / (intval($valeur[1]) + intval($valeur[0])))));
            }
        }

        dump($labeltab, $datatab);

        $tab = [
            'type' => 'bar',
            'data' => [
                'labels' => $labeltab,
                'datasets' => [
                    'label' => ['dsds'],
                    'data' => $datatab,
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
