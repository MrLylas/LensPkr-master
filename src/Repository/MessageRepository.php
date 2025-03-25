<?php

namespace App\Repository;

use App\Entity\Message;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Message>
 */
class MessageRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Message::class);
    }

    public function findReceivedMessage(?string $query, int $id): array
    {
        if (!is_null($query) && !is_string($query)) {
            throw new \InvalidArgumentException('La chaîne de recherche doit être un string');
        }
        if (!is_int($id)) {
            throw new \InvalidArgumentException('L\'ID doit être un entier');
        }
    
        $queryBuilder = $this->createQueryBuilder('m')
            ->innerJoin('m.sender', 's')
            ->andWhere('m.sender = :id')
            ->setParameter('id', $id);
    
        if ($query) {
            $queryBuilder
                ->andWhere("s.name LIKE :query OR s.pseudo LIKE :query OR s.forename LIKE :query OR s.email LIKE :query")
                ->setParameter('query', '%' . $query . '%');
        }
    
        return $queryBuilder
            ->getQuery()
            ->getResult();
    }
    // public function findSentMessage(?string $query): array
    // {
    //     return $this->createQueryBuilder('m')
    //         ->innerJoin('m.recipient', 'r')
    //         ->andWhere('m.sender.id = :id AND r.name LIKE :query OR r.pseudo LIKE :query OR r.forename LIKE :query OR r.email LIKE :query')
    //         ->setParameter( 'id', $this->getUser()->getId()) 
    //         ->setParameter( 'query', '%' . $query . '%')
    //         ->getQuery()
    //         ->getResult()
    //     ;
    // }

//    /**
//     * @return Message[] Returns an array of Message objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('m.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Message
//    {
//        return $this->createQueryBuilder('m')
//            ->andWhere('m.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
