<?php

namespace App\Repository;

use App\Entity\UserSkill;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserSkill>
 */
class UserSkillRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserSkill::class);
    }

    //    /**
    //     * @return UserSkill[] Returns an array of UserSkill objects
    //     */
    //    public function findByExampleField($value): array
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->orderBy('u.id', 'ASC')
    //            ->setMaxResults(10)
    //            ->getQuery()
    //            ->getResult()
    //        ;
    //    }

    //    public function findOneBySomeField($value): ?UserSkill
    //    {
    //        return $this->createQueryBuilder('u')
    //            ->andWhere('u.exampleField = :val')
    //            ->setParameter('val', $value)
    //            ->getQuery()
    //            ->getOneOrNullResult()
    //        ;
    //    }

    public function findSkillNotInUser($user_id){

        $entityManager = $this->getEntityManager();
        $subQuery = $entityManager->createQueryBuilder();

        $qb = $subQuery;

        $qb->select('t');
        $qb->from('App\Entity\Skill', 't');
        $qb->leftJoin('t.userSkills', 'u');
        $qb->where('u.id = :id');

        $subQuery = $entityManager->createQueryBuilder();

        $subQuery->select('tr');
        $subQuery->from('App\Entity\Skill', 'tr');
        $subQuery->where($qb->expr()->notIn('tr.id', $qb->getDQL()));
        $subQuery->setParameter('id', $user_id);
        // $subQuery->orderBy('tr.skill_name', 'ASC');

        $query = $subQuery->getQuery();

        return $query->getResult();
    }

    // public function sortSkillBySpeciality(){

    //     $entityManager = $this->getEntityManager();
    //     $subQuery = $entityManager->createQueryBuilder();

    //     $qb = $subQuery;

    //     $qb->select('t');
    //     $qb->from('App\Entity\Skill', 't');
    //     $qb->leftJoin('t.userSkills', 'u');
    //     $qb->where('u.id = :id');

    //     $subQuery = $entityManager->createQueryBuilder();   

    //     // $subQuery->select('tr');
    //     // $subQuery->from('App\Entity\Skill', 'tr');
    //     // $subQuery->where($qb->expr()->notIn('tr.id', $qb->getDQL()));
    //     // $subQuery->setParameter('id', $user_id);
    //     // $subQuery->orderBy('tr.speciality', 'ASC');

    //     $query = $subQuery->getQuery();

    //     return $query->getResult();
    // }
}
