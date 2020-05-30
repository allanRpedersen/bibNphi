<?php

namespace App\Repository;

use App\Entity\BookParagraph;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method BookParagraph|null find($id, $lockMode = null, $lockVersion = null)
 * @method BookParagraph|null findOneBy(array $criteria, array $orderBy = null)
 * @method BookParagraph[]    findAll()
 * @method BookParagraph[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookParagraphRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, BookParagraph::class);
    }

    // /**
    //  * @return BookParagraph[] Returns an array of BookParagraph objects
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
    public function findOneBySomeField($value): ?BookParagraph
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
