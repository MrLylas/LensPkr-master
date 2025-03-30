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

    public function findReceivedMessage(?string $query): array
    {
        return $this->createQueryBuilder('m')
            ->where('m.title LIKE :query')
            ->setParameter( 'query', '%' . $query . '%')
            ->getQuery()
            ->getResult()
        ;
    }
    public function findSentMessage(?string $query): array
    {
        return $this->createQueryBuilder('m')
            ->innerJoin('m.recipient', 'r')
            ->where('r.name LIKE :query OR r.pseudo LIKE :query OR r.forename LIKE :query OR r.email LIKE :query OR m.title LIKE :query')
            ->setParameter( 'query', '%' . $query . '%')
            ->getQuery()
            ->getResult()
        ;
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
