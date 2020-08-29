<?php

namespace App\Repository;

use App\Entity\Author;
use Doctrine\ORM\Query;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;

/**
 * @method Author|null find($id, $lockMode = null, $lockVersion = null)
 * @method Author|null findOneBy(array $criteria, array $orderBy = null)
 * @method Author[]    findAll()
 * @method Author[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class AuthorRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Author::class);
    }


	/**
	 * 
	 * 
	 * findByLastName
	 * 
	 * @param string $orderBy
     * @return Author[] Returns an array of Author objects
     */
    public function findByLastName($orderBy='ASC')
    {
        return $this->createQueryBuilder('t')
            //->andWhere('t.lastName = :val')
            //->setParameter('val', $value)
            ->orderBy('t.lastName', $orderBy)
            //->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }

	/**
	 * 
	 * 
	 * findByLastNameQuery
	 * 
	 * @param string $orderBy
     * @return Query Returns a query
     */
    public function findByLastNameQuery($orderBy='ASC') : Query
    {
        return $this->createQueryBuilder('t')
            //->andWhere('t.lastName = :val')
            //->setParameter('val', $value)
            ->orderBy('t.lastName', $orderBy)
            //->setMaxResults(10)
            ->getQuery()
        ;
    }


    // /**
    //  * @return Author[] Returns an array of Author objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Author
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
