<?php

namespace App\Repository;


use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ObjectManager;

class StatRepository extends AbstractController
{
    /**
     * ObjectManager
     */
    private $objectManager;

    public function __construct()
    {
        //Nope
    }

    public function setObjectManager(ObjectManager $pobjectManager)
    {
        $this->objectManager = $pobjectManager;
    }

    private function getColor($num) {
        return array(
            rand(20,230), // r
            rand(20,230), // g
            rand(20,230)); //b
    }

    private function randomColor() {
        $color = $this->getColor(2);

        $tabColor = [
            'backgroundColor' => "rgba(".$color[0].", ".$color[1].", ".$color[2].", 0.2)",
            'borderColor' => "rgba(".$color[0].", ".$color[1].", ".$color[2].", 1)"
        ];
        return ($tabColor);
    }

    private function buildTab(Array $filterTab){
        $tab = ['data' => ['datasets' =>  array()]];

        foreach($filterTab as $clef => $valeur){
            $colors = $this->randomColor();

            //Pas de présentiel
            if(false == isset($valeur[0])){
                $valeur = 100;
            //Pas de télétravail
            }else if(false == isset($valeur[1])){
                $valeur = 0;
                //Les deux
            }else{
                $valeur = round(100 * (intval($valeur[1]) / (intval($valeur[1]) + intval($valeur[0]))));
            }

            array_push($tab['data']['datasets'],
                [
                    'label' => $clef,
                    'data' => array($valeur, 100-$valeur),
                    'backgroundColor' => $colors['backgroundColor'],
                    'borderColor' => $colors['borderColor'],
                    'borderWidth' => 1
                ]
            );
        }

        return $tab;
    }

    /**
     * Pourcentage du temps de travail par salarié en présentiel ou télétravail, sur le mois de 02/2021, par salariés
     */
    public function req1(string $monthYear = '2/2021')
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
                HAVING monthYear = '".$monthYear."'
        
                UNION
                
                SELECT SUM(TIMEDIFF(ADDTIME(LAST_DAY(t.date_time_start), '24:00:00'), t.date_time_start)) AS nbrTime, CONCAT(MONTH(t.date_time_start), '/', YEAR(t.date_time_start)) AS monthYear, CONCAT(u.firstname, ' ', u.lastname) as nameUser, wt.is_teleworked as teleworked
                FROM task t, user u, work_time wt
                WHERE t.user_id = u.id
                          AND t.work_time_id = wt.id
                          AND MONTH(t.date_time_start) <> MONTH(t.date_time_end)
                
                GROUP BY monthYear, nameUser, teleworked
                HAVING monthYear = '".$monthYear."'
                
                UNION
                
                SELECT SUM(TIMEDIFF(t.date_time_end, ADDTIME(LAST_DAY(t.date_time_start), '24:00:00'))) AS nbrTime, CONCAT(MONTH(t.date_time_end), '/', YEAR(t.date_time_end)) AS monthYear, CONCAT(u.firstname, ' ', u.lastname) as nameUser, wt.is_teleworked as teleworked
                FROM task t, user u, work_time wt
                WHERE t.user_id = u.id
                          AND t.work_time_id = wt.id
                          AND MONTH(t.date_time_start) <> MONTH(t.date_time_end)
                
                GROUP BY monthYear, nameUser, teleworked
                HAVING monthYear = '".$monthYear."'
            ) AS Stat1
        
        GROUP BY monthYear, nameUser, teleworked
        ORDER BY monthYear, nameUser, teleworked";

        $statement = $this->objectManager->getConnection()->prepare($RAW_QUERY);
        $statement->execute();

        $result = $statement->fetchAll();

        //Filtrage pour un traitement plus simple
        for($i=0; $i < count($result); $i++){
            $filterTab[$result[$i]['nameUser']][$result[$i]['teleworked']] = intval($result[$i]['nbrTime']);
        }

        $tab = $this->buildTab($filterTab);

        return json_encode($tab);
    }

    /**
     * Pourcentage du temps de travail en présentiel ou télétravail, sur le mois de 02/2021, par « personnel non cadre » ou « personnel cadre »
     */
    public function req2(string $monthYear = '2/2021')
    {
        $RAW_QUERY = "
        SELECT SUM(Stat1.nbrTime) AS nbrTime, Stat1.monthYear AS monthYear, Stat1.executive as executive, Stat1.teleworked as teleworked
        FROM (
            SELECT SUM(TIMEDIFF(t.date_time_end, t.date_time_start)) AS nbrTime, CONCAT(MONTH(t.date_time_start), '/', YEAR(t.date_time_start)) AS monthYear, u.is_executive as executive, wt.is_teleworked as teleworked
            FROM task t, user u, work_time wt
            WHERE t.user_id = u.id
                AND t.work_time_id = wt.id
                AND MONTH(t.date_time_start) = MONTH(t.date_time_end)
        
            GROUP BY monthYear, executive, teleworked
            HAVING monthYear = '".$monthYear."'
        
            UNION
        
            SELECT SUM(TIMEDIFF(ADDTIME(LAST_DAY(t.date_time_start), '24:00:00'), t.date_time_start)) AS nbrTime, CONCAT(MONTH(t.date_time_start), '/', YEAR(t.date_time_start)) AS monthYear, u.is_executive as executive, wt.is_teleworked as teleworked
            FROM task t, user u, work_time wt
            WHERE t.user_id = u.id
                AND t.work_time_id = wt.id
                AND MONTH(t.date_time_start) <> MONTH(t.date_time_end)
        
            GROUP BY monthYear, executive, teleworked
            HAVING monthYear = '".$monthYear."'
        
            UNION
        
            SELECT SUM(TIMEDIFF(t.date_time_end, ADDTIME(LAST_DAY(t.date_time_start), '24:00:00'))) AS nbrTime, CONCAT(MONTH(t.date_time_end), '/', YEAR(t.date_time_end)) AS monthYear, u.is_executive as executive, wt.is_teleworked as teleworked
            FROM task t, user u, work_time wt
            WHERE t.user_id = u.id
                AND t.work_time_id = wt.id
                AND MONTH(t.date_time_start) <> MONTH(t.date_time_end)
        
            GROUP BY monthYear, executive, teleworked
            HAVING monthYear = '".$monthYear."'
            ) AS Stat1
        
        GROUP BY monthYear, executive, teleworked
        ";

        $statement = $this->objectManager->getConnection()->prepare($RAW_QUERY);
        $statement->execute();

        $result = $statement->fetchAll();

        //Filtrage pour un traitement plus simple
        for($i=0; $i < count($result); $i++){
            if($result[$i]['executive'] == "1")
                $result[$i]['executive'] = "cadre";
            else
                $result[$i]['executive'] = "non cadre";

            $filterTab[$result[$i]['executive']][$result[$i]['teleworked']] = intval($result[$i]['nbrTime']);
        }

        $tab = $this->buildTab($filterTab);

        dump($tab);

        return json_encode($tab);
    }
}