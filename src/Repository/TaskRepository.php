<?php

namespace App\Repository;

use App\Entity\PlanningSearch;
use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\QueryBuilder;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Task|null find($id, $lockMode = null, $lockVersion = null)
 * @method Task|null findOneBy(array $criteria, array $orderBy = null)
 * @method Task[]    findAll()
 * @method Task[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TaskRepository extends ServiceEntityRepository {

    /**
     * TaskRepository constructor.
     * @param ManagerRegistry $registry
     */
    public function __construct(ManagerRegistry $registry) {
        parent::__construct($registry, Task::class);
    }

    /**
     * @param $userId
     * @return Task[]
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
     * @param $userId
     * @param PlanningSearch $search
     * @return Task[]
     */
    public function findAllFiltered(int $userId, PlanningSearch $search): array {
        $query = $this->findAllByUserQuery($userId);

        if($search->getTaskCategories()->count() > 0) {
            foreach ($search->getTaskCategories() as $k => $category) {
                $query =$query
                    ->andWhere(':taskCategory == p.taskCategory')
                    ->setParameter('taskCategory', $category);
            }
        }

        return $query->getQuery()->getResult();
    }
}
