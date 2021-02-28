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
            $tab['titre'] = "Pourcentage du temps de travail par salarié en télétravail ou présentiel, par salariés";
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
            $tab['titre'] = "Pourcentage du temps de travail en présentiel ou télétravail, par personnel « cadre » ou « non cadre »";
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
            $tab['titre'] = "Pourcentage des périodes passées en télétravail ou en présentiel par salariés";
        }

        return json_encode($tab);
    }

    /**
     * D’avoir un récapitulatif mensuel cumulé des tâches effectuées par un salarié.
     * Ce récapitulatif devra être sous le format suivant : Catégorie – Temps passé. Le détail des « commentaires » ne devra pas apparaître
     */
    public function req3(string $monthYear)
    {
        $RAW_QUERY="
        SELECT Stat1.userId as userId, Stat1.monthYear AS monthYear, Stat1.categ as categ, SUM(Stat1.nbrTime) AS nbrTime
        FROM (
                 SELECT SUM(TIMEDIFF(t.date_time_end, t.date_time_start)) AS nbrTime, CONCAT(MONTH(t.date_time_start), '/', YEAR(t.date_time_start)) AS monthYear, tc.id as categ, u.id as userId
                 FROM task t, user u, task_category tc
                 WHERE t.user_id = u.id
                           AND t.task_category_id = tc.id
                           AND MONTH(t.date_time_start) = MONTH(t.date_time_end)
        
        GROUP BY monthYear, categ, userId
        HAVING monthYear = '".$monthYear."'
        
        UNION
        
        SELECT SUM(TIMEDIFF(ADDTIME(LAST_DAY(t.date_time_start), '24:00:00'), t.date_time_start)) AS nbrTime, CONCAT(MONTH(t.date_time_start), '/', YEAR(t.date_time_start)) AS monthYear, tc.id as categ, u.id as userId
        FROM task t, user u, task_category tc
        WHERE t.user_id = u.id
                  AND t.task_category_id = tc.id
                  AND MONTH(t.date_time_start) <> MONTH(t.date_time_end)
        
        GROUP BY monthYear, categ, userId
        HAVING monthYear = '".$monthYear."'
        
        UNION
        
        SELECT SUM(TIMEDIFF(t.date_time_end, ADDTIME(LAST_DAY(t.date_time_start), '24:00:00'))) AS nbrTime, CONCAT(MONTH(t.date_time_end), '/', YEAR(t.date_time_end)) AS monthYear, tc.id as categ, u.id as userId
        FROM task t, user u, task_category tc
        WHERE t.user_id = u.id
                  AND t.task_category_id = tc.id
                  AND MONTH(t.date_time_start) <> MONTH(t.date_time_end)
        
        GROUP BY monthYear, categ, userId
        HAVING monthYear = '".$monthYear."'
        
            ) AS Stat1
        
        GROUP BY monthYear, categ, userId
        ORDER BY monthYear, userId, categ";

        $statement = $this->objectManager->getConnection()->prepare($RAW_QUERY);
        $statement->execute();

        $result = $statement->fetchAll();



        //Filtrage pour un traitement plus simple
        for($i=0; $i < count($result); $i++){
            $filterTab[$result[$i]['nameUser']][$result[$i]['teleworked']] = intval($result[$i]['nbrDays']);
        }

        $tab = $this->buildTab($filterTab);
        $tab['titre'] = "02/2021";

        return json_encode($tab);
    }
}