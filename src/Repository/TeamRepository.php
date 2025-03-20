<?php

namespace App\Repository;

use App\Entity\Team;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Team>
 */
class TeamRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Team::class);
    }

    public function recentsTeams()
    {
        return $this->createQueryBuilder('t')
            ->orderBy('t.created_at', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function myTeams($user_id)
    {
        return $this->createQueryBuilder('t')
            ->innerJoin('t.membership', 'm')
            ->andWhere('m.id = :user_id')
            ->setParameter('user_id', $user_id)
            ->getQuery()
            ->getResult()
        ;
    }

    public function popularTeams()
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.membership', 'm')
            ->groupBy('t.id')
            ->orderBy('COUNT(m.id)', 'ASC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function followedTeams($user_id)
    {
        return $this->createQueryBuilder('t')
            ->innerJoin('t.follow', 'f')
            ->andWhere('f.id = :user_id')
            ->setParameter('user_id', $user_id)
            ->getQuery()
            ->getResult()
        ;
    }
    public function searchTeams(?string $query): array
    {

    if (!$query) {
        return [];
    }

    return $this->createQueryBuilder('t')
        ->where('t.name LIKE :query')
        ->setParameter('query', '%' . $query . '%')
        ->getQuery()
        ->getResult();
    }


//    /**
//     * @return Team[] Returns an array of Team objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('t.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Team
//    {
//        return $this->createQueryBuilder('t')
//            ->andWhere('t.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
