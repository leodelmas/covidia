<?php

namespace App\Repository;

use App\Entity\WorkTime;
use DateTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method WorkTime|null find($id, $lockMode = null, $lockVersion = null)
 * @method WorkTime|null findOneBy(array $criteria, array $orderBy = null)
 * @method WorkTime[]    findAll()
 * @method WorkTime[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WorkTimeRepository extends ServiceEntityRepository {

    /**
     * WorkTimeRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, WorkTime::class);
    }

    /**
     * @param $userId
     * @return WorkTime[]
     */
    public function findAllByUser($userId): array {
        return $this->findAllByUserQuery($userId)->getQuery()->getResult();
    }

    /**
     * @param $userId
     * @return QueryBuilder
     */
    public function findAllByUserQuery($userId): QueryBuilder {
        return $this->createQueryBuilder('p')
            ->andWhere('p.user = :userId')
            ->setParameter('userId', $userId);
    }

    /**
     * @param int $userId
     * @param DateTime $dateTimeStart
     * @param DateTime $dateTimeEnd
     * @return WorkTime|null
     * @throws NonUniqueResultException
     */
    public function findRightWorkTimeForTask(int $userId, DateTime $dateTimeStart, DateTime $dateTimeEnd): ?WorkTime {
        $query = $this->findAllByUserQuery($userId);
        return $query
            ->andWhere('p.dateStart <= :dateTimeStart')
            ->andWhere('p.dateEnd >= :dateTimeEnd')
            ->setParameter('dateTimeStart', $dateTimeStart->format('Y-m-d'))
            ->setParameter('dateTimeEnd', $dateTimeEnd->format('Y-m-d'))
            ->getQuery()
            ->getOneOrNullResult();
    }


    /**
     * @param $userId
     * @param $dateStart
     * @param $dateEnd
     * @return WorkTime|null
     * @throws NonUniqueResultException
     */
    public function findAlreadyPlannedWorkTime($userId, $dateStart, $dateEnd): ?WorkTime {
        return $this->createQueryBuilder('p')
            ->andWhere(':dateStart BETWEEN p.dateStart AND p.dateEnd')
            ->orWhere(':dateEnd BETWEEN p.dateStart AND p.dateEnd')
            ->orWhere('p.dateStart BETWEEN :dateStart AND :dateEnd')
            ->orWhere('p.dateEnd BETWEEN :dateStart AND :dateEnd')
            ->andWhere('p.user = :userId')
            ->setParameter('dateStart', $dateStart->format('Y-m-d'))
            ->setParameter('dateEnd', $dateEnd->format('Y-m-d'))
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
