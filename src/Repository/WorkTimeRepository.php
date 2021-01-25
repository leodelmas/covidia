<?php

namespace App\Repository;

use App\Entity\WorkTime;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\NonUniqueResultException;
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
    public function findAllByUser($userId) {
        return $this->createQueryBuilder('p')
            ->andWhere('p.user = :userId')
            ->setParameter('userId', $userId)
            ->getQuery()
            ->getResult();
    }

    /**
     * @param $userId
     * @param $dateTimeStart
     * @param $dateTimeEnd
     * @return WorkTime
     * @throws NonUniqueResultException
     */
    public function findRightWorkTimeForTask($userId, $dateTimeStart, $dateTimeEnd) {
        return $this->createQueryBuilder('p')
            ->andWhere('p.user = :userId')
            ->andWhere('p.dateStart <= :dateTimeStart')
            ->andWhere('p.dateEnd >= :dateTimeEnd')
            ->setParameter('userId', $userId)
            ->setParameter('dateTimeStart', $dateTimeStart->format('Y-m-d'))
            ->setParameter('dateTimeEnd', $dateTimeEnd->format('Y-m-d'))
            ->getQuery()
            ->getOneOrNullResult();
    }
}
