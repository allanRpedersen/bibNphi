<?php

namespace App\Repository;

use App\Entity\Book;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Query;

/**
 * @method Book|null find($id, $lockMode = null, $lockVersion = null)
 * @method Book|null findOneBy(array $criteria, array $orderBy = null)
 * @method Book[]    findAll()
 * @method Book[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class BookRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Book::class);
    }

    /**
	 * findByTitle
	 * 
	 * @param string $orderBy
     * @return Book[] Returns an array of Book objects
     */
    public function findByTitle($orderBy='ASC') : array
    {
        return $this->createQueryBuilder('t')
            //->andWhere('t.title = :val')
            //->setParameter('val', $value)
            ->orderBy('t.title', $orderBy)
            //->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

    /**
	 * findByTitleQuery
	 * 
	 * @param string $orderBy
     * @return Query Returns a query
     */
    public function findByTitleQuery($orderBy='ASC') : Query
    {
        return $this->createQueryBuilder('t')
            //->andWhere('t.title = :val')
            //->setParameter('val', $value)
            ->orderBy('t.title', $orderBy)
            //->setMaxResults(10)
            ->getQuery()
        ;
    }


    // /**
    //  * @return Book[] Returns an array of Book objects
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
    public function findOneBySomeField($value): ?Book
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
