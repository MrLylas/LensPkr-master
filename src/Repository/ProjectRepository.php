<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Project>
 */
class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
    }

    public function recentProjects()
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.created_at', 'DESC')
            ->getQuery()
            ->getResult()
        ;
    }

    public function teamProjects($team_id)
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.team = :team_id')
            ->setParameter('team_id', $team_id)
            ->getQuery()
            ->getResult()
        ;
    }
    public function popularProjects()
    {
    return $this->createQueryBuilder('p')
        ->leftJoin('p.likes', 'l')
        ->groupBy('p.id')
        ->orderBy('COUNT(l.id)', 'DESC')
        ->getQuery()
        ->getResult();
    }

    public function LikedProjects($user_id)
{
    return $this->createQueryBuilder('p')
    ->innerJoin('p.likes', 'u')
    ->andWhere('u.id = :user_id')
    ->setParameter('user_id', $user_id)
    ->getQuery()
    ->getResult()
    ;
}

public function MyProjects($user_id)
{
    return $this->createQueryBuilder('p')
    ->andWhere('p.creator = :user_id')
    ->setParameter('user_id', $user_id)
    ->orderBy('p.created_at', 'DESC')
    ->getQuery()
    ->getResult()
    ;
}

public function MyTeamsProjects($user_id)
{
    return $this->createQueryBuilder('p')
    ->innerJoin('p.team', 't')  
    ->innerJoin('t.membership', 'm')  
    ->andWhere('m.id = :user_id')  
    ->setParameter('user_id', $user_id)
    ->getQuery()
    ->getResult();
}

public function searchProjects(?string $query): array
{
    // Si la requête de recherche est vide on retourne un tableau vide, ça facilite le traitement et la mise en page
    if (!$query) {
        return [];
    }

    return $this->createQueryBuilder('p')
        // On recherche dans les champs projectName et description
        ->where('p.projectName LIKE :query OR p.description LIKE :query')
        // On utilise %query% comme parametre de recherche
        ->setParameter('query', '%' . $query . '%')
        ->getQuery()
        ->getResult();
}




//    /**
//     * @return Project[] Returns an array of Project objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('p.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Project
//    {
//        return $this->createQueryBuilder('p')
//            ->andWhere('p.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
