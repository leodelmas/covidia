<?php

namespace App\Repository;


use App\Entity\TaskCategory;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Doctrine\Persistence\ObjectManager;

class StatsRepository extends AbstractController
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
     * Pourcentage du temps de travail par salarié en présentiel ou télétravail, par salariés
     */
    public function req1(string $monthYear)
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

        $tab = array();
        if(true == isset($filterTab)){
            $tab = $this->buildTab($filterTab);
        }

        return json_encode($tab);
    }

    /**
     * Pourcentage du temps de travail en présentiel ou télétravail, par « personnel non cadre » ou « personnel cadre »
     */
    public function req2(string $monthYear)
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

        $tab = array();
        if(true == isset($filterTab)){
            $tab = $this->buildTab($filterTab);
        }

        return json_encode($tab);
    }

    /**
     * Pourcentage des périodes passées en télétravail ou en présentiel par salariés
     */
    public function req5(string $monthYear)
    {
        $RAW_QUERY="
        SELECT Stat1.monthYear, Stat1.nameUser, Stat1.teleworked, SUM(Stat1.days) as nbrDays
        FROM (
                 SELECT SUM(DATEDIFF(date_end,  date_start) + 1) as days, is_teleworked as teleworked, CONCAT(MONTH(date_end), '/', YEAR(date_end)) AS monthYear,
                    CONCAT(user.firstname, ' ', user.lastname) as nameUser
                 FROM work_time, user
                 WHERE MONTH(date_start) =  MONTH(date_end)
                   AND user_id = user.id
        
                 GROUP BY is_teleworked, monthYear, nameUser
                 HAVING monthYear = '".$monthYear."'
        
                 UNION
        
                 SELECT SUM(DATEDIFF(LAST_DAY(date_start),  date_start) + 1) as days, is_teleworked as teleworked, CONCAT(MONTH(date_start), '/', YEAR(date_start)) AS monthYear,											
                    CONCAT(firstname, ' ', lastname) as nameUser
                 FROM work_time, user
                 WHERE MONTH(date_start) <>  MONTH(date_end)
                   AND user_id = user.id
        
                 GROUP BY is_teleworked, monthYear, nameUser
                 HAVING monthYear = '".$monthYear."'
        
                 UNION
        
                 SELECT SUM(DATEDIFF(date_end,  date_add(date_end,interval -DAY(date_end)+1 DAY)) + 1) as days, is_teleworked as teleworked, CONCAT(MONTH(date_end), '/', YEAR(date_end)) AS monthYear,
                    CONCAT(user.firstname, ' ', user.lastname) as nameUser
                 FROM work_time, user
                 WHERE MONTH(date_start) <>  MONTH(date_end)
                   AND user_id = user.id
        
                 GROUP BY is_teleworked, monthYear, nameUser
                 HAVING monthYear = '".$monthYear."'
        
             ) as Stat1
        
        GROUP BY Stat1.monthYear, Stat1.teleworked, Stat1.nameUser
        ORDER BY Stat1.monthYear, Stat1.teleworked, Stat1.nameUser";

        $statement = $this->objectManager->getConnection()->prepare($RAW_QUERY);
        $statement->execute();

        $result = $statement->fetchAll();

        //Filtrage pour un traitement plus simple
        for($i=0; $i < count($result); $i++){
            $filterTab[$result[$i]['nameUser']][$result[$i]['teleworked']] = intval($result[$i]['nbrDays']);
        }

        $tab = array();
        if(true == isset($filterTab)){
            $tab = $this->buildTab($filterTab);
        }

        return json_encode($tab);
    }

    /**
     * D’avoir un récapitulatif mensuel cumulé des tâches effectuées par un salarié.
     * Ce récapitulatif devra être sous le format suivant : Catégorie – Temps passé. Le détail des « commentaires » ne devra pas apparaître
     */
    public function req3(string $monthYear, $taskCategory)
    {
        $RAW_QUERY="
        SELECT Stat1.nameUser as nameUser, Stat1.monthYear AS monthYear, Stat1.categ as categ, SUM(Stat1.nbrTime) AS nbrTime
        FROM (
                 SELECT SUM(TIMEDIFF(t.date_time_end, t.date_time_start)) AS nbrTime, CONCAT(MONTH(t.date_time_start), '/', 
                        YEAR(t.date_time_start)) AS monthYear, tc.id as categ, CONCAT(u.firstname, ' ', u.lastname) as nameUser
                 FROM task t, user u, task_category tc
                 WHERE t.user_id = u.id
                           AND t.task_category_id = tc.id
                           AND MONTH(t.date_time_start) = MONTH(t.date_time_end)
        
                GROUP BY monthYear, categ, nameUser
                HAVING monthYear = '".$monthYear."'
                
                UNION
                
                SELECT SUM(TIMEDIFF(ADDTIME(LAST_DAY(t.date_time_start), '24:00:00'), t.date_time_start)) AS nbrTime, CONCAT(MONTH(t.date_time_start), '/', 
                    YEAR(t.date_time_start)) AS monthYear, tc.id as categ, CONCAT(u.firstname, ' ', u.lastname) as nameUser
                FROM task t, user u, task_category tc
                WHERE t.user_id = u.id
                          AND t.task_category_id = tc.id
                          AND MONTH(t.date_time_start) <> MONTH(t.date_time_end)
                
                GROUP BY monthYear, categ, nameUser
                HAVING monthYear = '".$monthYear."'
                
                UNION
                
                SELECT SUM(TIMEDIFF(t.date_time_end, ADDTIME(LAST_DAY(t.date_time_start), '24:00:00'))) AS nbrTime, CONCAT(MONTH(t.date_time_end), '/', 
                    YEAR(t.date_time_end)) AS monthYear, tc.id as categ, CONCAT(u.firstname, ' ', u.lastname) as nameUser
                FROM task t, user u, task_category tc
                WHERE t.user_id = u.id
                          AND t.task_category_id = tc.id
                          AND MONTH(t.date_time_start) <> MONTH(t.date_time_end)
                
                GROUP BY monthYear, categ, nameUser
                HAVING monthYear = '".$monthYear."'
        
            ) AS Stat1
        
        GROUP BY monthYear, categ, nameUser
        ORDER BY monthYear, nameUser, categ";

        $statement = $this->objectManager->getConnection()->prepare($RAW_QUERY);
        $statement->execute();

        $result = $statement->fetchAll();
        $tab = array();

        //Filtrage pour un traitement plus simple
        for($i=0; $i < count($result); $i++){
            $nbrTime = $result[$i]['nbrTime'];
            $hours = explode('.', ($nbrTime / 10000) % 100)[0];
            $minutes = explode('.', ($nbrTime / 100) % 100)[0];
            $formatNbrTime = $hours."h".$minutes;
            $tab['data'][$result[$i]['nameUser']][$result[$i]['categ']] = $formatNbrTime;
        }

        if(true == isset($tab['data'])){
            foreach ($tab['data'] as $user => $tabCateg) {
                for($i=0; $i < count($taskCategory); $i++)
                {
                    $idCateg = $taskCategory[$i]->getId();
                    if(false == isset($tabCateg[$idCateg]))
                    {
                        $tab['data'][$user][$idCateg] = "0h0";
                    }
                }
            }
        }

        return $tab;
    }

    /**
     * D’avoir une moyenne d’âge mensuelle des personnes en télétravail et en présentiel.
     */
    public function req4(string $monthYear)
    {
        $RAW_QUERY="
        SELECT ROUND(AVG(TIMESTAMPDIFF(year,u.birth_date, CURRENT_DATE ))) AS MoyAge, wt.is_teleworked as teleworked
        FROM task t, user u, work_time wt
        WHERE t.user_id = u.id
            AND t.work_time_id = wt.id
            AND (
                CONCAT(MONTH(t.date_time_start), '/', YEAR(t.date_time_start)) = '".$monthYear."'
                OR
                CONCAT(MONTH(t.date_time_end), '/', YEAR(t.date_time_end)) = '".$monthYear."'
            )
            
            GROUP BY teleworked";

        $statement = $this->objectManager->getConnection()->prepare($RAW_QUERY);
        $statement->execute();

        $result = $statement->fetchAll();
        $tab = array();

        if(count($result) < 1)
        {
            return $tab;
        }

        //Filtrage pour un traitement plus simple
        for($i=0; $i < count($result); $i++){
            if($result[$i]['teleworked'] == 1)
            {
                $moyAgeTele = $result[$i]['MoyAge'];
            }else
            {
                $moyAge = $result[$i]['MoyAge'];
            }
        }

        if(isset($moyAgeTele) == false)
            $moyAgeTele = 0;
        if(isset($moyAge) == false)
            $moyAge = 0;

        $tab['moyAgeTele'] = $moyAgeTele;
        $tab['moyAge'] = $moyAge;

        return $tab;
    }
}