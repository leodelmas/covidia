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

- D’avoir le pourcentage du temps de travail en entreprise ou en télétravail, par mois, et par salariés

SELECT Stat1.nbrHoursNotTeleworked
FROM (
         SELECT SUM(TIME_TO_SEC(t.date_time_end) - TIME_TO_SEC(t.date_time_start)) AS nbrHoursTeleworked, CONCAT(MONTH(t.date_time_start), '/', YEAR(t.date_time_start)) AS monthYear, 			CONCAT(u.firstname, ' ', u.lastname) as nameUser
         FROM task t, user u, work_time wt
         WHERE t.user_id = u.id
           AND t.work_time_id = wt.id
           AND wt.is_teleworked = 1

         GROUP BY monthYear, nameUser

         UNION

         SELECT SUM(TIME_TO_SEC(date_time_end) - TIME_TO_SEC(date_time_start)) AS nbrHoursNotTeleworked, CONCAT(MONTH(date_time_start), '/', YEAR(date_time_start)) AS monthYear, 						CONCAT(user.firstname, ' ', user.lastname) as nameUser
         FROM task, user, work_time
         WHERE task.user_id = user.id
           AND work_time_id = work_time.id
           AND is_teleworked = 0

         GROUP BY monthYear, nameUser

     ) as Stat1