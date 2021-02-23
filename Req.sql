- Pourcentage de jours par mois / teleworked ou pas / par salarié (J'avais mal compris la 1, donc c'est cadeau)

SELECT Stat1.month, Stat1.nameUser, Stat1.teleworked, SUM(Stat1.days) as nbrDays
FROM (
         SELECT SUM(DATEDIFF(date_end,  date_start) + 1) as 'days', is_teleworked as 'teleworked', MONTH(date_end) as 'month',
     		CONCAT(`user`.`firstname`, ' ', `user`.`lastname`) as 'nameUser'
         FROM `work_time`, `user`
         WHERE MONTH(date_start) =  MONTH(date_end)
           AND user_id = `user`.id

         GROUP BY is_teleworked, month, nameUser

         UNION

         SELECT SUM(DATEDIFF(LAST_DAY(date_start),  date_start) + 1) as 'days', is_teleworked as 'teleworked', MONTH(date_start) as 'month', 													CONCAT(`user`.`firstname`, ' ', `user`.`lastname`) as 'nameUser'
         FROM work_time, user
         WHERE MONTH(date_start) <>  MONTH(date_end)
           AND user_id = `user`.id

         GROUP BY is_teleworked, month, nameUser

         UNION

         SELECT SUM(DATEDIFF(date_end,  date_add(date_end,interval -DAY(date_end)+1 DAY)) + 1) as 'days', is_teleworked as 'teleworked', MONTH(date_end) as 'month', 							CONCAT(`user`.`firstname`, ' ', `user`.`lastname`) as 'nameUser'
         FROM `work_time`, `user`
         WHERE MONTH(date_start) <>  MONTH(date_end)
           AND user_id = `user`.id

         GROUP BY is_teleworked, month, nameUser

     ) as Stat1

GROUP BY Stat1.month, Stat1.teleworked, Stat1.nameUser
ORDER BY Stat1.month, Stat1.teleworked, Stat1.nameUser

- D’avoir un récapitulatif mensuel cumulé des tâches effectuées par un salarié. Ce récapitulatif devra être sous le format suivant : Catégorie – Temps passé. Le détail des « commentaires » ne devra pas apparaître

SELECT Stat1.userId as userId, Stat1.monthYear AS monthYear, Stat1.categ as categ, SUM(Stat1.nbrTime) AS nbrTime
FROM (
         SELECT SUM(TIMEDIFF(t.date_time_end, t.date_time_start)) AS nbrTime, CONCAT(MONTH(t.date_time_start), '/', YEAR(t.date_time_start)) AS monthYear, tc.name as categ, u.id as userId
         FROM task t, user u, task_category tc
         WHERE t.user_id = u.id
                   AND t.task_category_id = tc.id
                   AND MONTH(t.date_time_start) = MONTH(t.date_time_end)

GROUP BY monthYear, categ, userId

UNION

SELECT SUM(TIMEDIFF(ADDTIME(LAST_DAY(t.date_time_start), '24:00:00'), t.date_time_start)) AS nbrTime, CONCAT(MONTH(t.date_time_start), '/', YEAR(t.date_time_start)) AS monthYear, tc.name as categ, u.id as userId
FROM task t, user u, task_category tc
WHERE t.user_id = u.id
          AND t.task_category_id = tc.id
          AND MONTH(t.date_time_start) <> MONTH(t.date_time_end)

GROUP BY monthYear, categ, userId

UNION

SELECT SUM(TIMEDIFF(t.date_time_end, ADDTIME(LAST_DAY(t.date_time_start), '24:00:00'))) AS nbrTime, CONCAT(MONTH(t.date_time_end), '/', YEAR(t.date_time_end)) AS monthYear, tc.name as categ, u.id as userId
FROM task t, user u, task_category tc
WHERE t.user_id = u.id
          AND t.task_category_id = tc.id
          AND MONTH(t.date_time_start) <> MONTH(t.date_time_end)

GROUP BY monthYear, categ, userId
    ) AS Stat1

GROUP BY monthYear, categ, userId
ORDER BY monthYear, userId, categ