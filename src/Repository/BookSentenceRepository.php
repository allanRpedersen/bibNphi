<?php

namespace App\Repository;

use App\Entity\BookSentence;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BookSentence|null find($id, $lockMode = null, $lockVersion = null)
 * @method BookSentence|null findOneBy(array $criteria, array $orderBy = null)
 * @method BookSentence[]    findAll()
 * @method BookSentence[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookSentenceRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookSentence::class);
    }

    // /**
    //  * @return BookSentence[] Returns an array of BookSentence objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('b.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?BookSentence
    {
        return $this->createQueryBuilder('b')
            ->andWhere('b.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
