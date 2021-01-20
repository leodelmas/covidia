<?php

namespace App\Repository;

use App\Entity\WorkingDay;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method WorkingDay|null find($id, $lockMode = null, $lockVersion = null)
 * @method WorkingDay|null findOneBy(array $criteria, array $orderBy = null)
 * @method WorkingDay[]    findAll()
 * @method WorkingDay[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class WorkingDayRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, WorkingDay::class);
    }

    /**
     * @param $userId
     * @return WorkingDay[]
     */
    public function findAllByUser($userId): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.user = 8')
            //->setParameter('userid', $userId)
            ->getQuery()
            ->getResult()
        ;
    }
}
