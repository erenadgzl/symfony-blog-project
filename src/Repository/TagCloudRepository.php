<?php

namespace App\Repository;

use App\Entity\TagCloud;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method TagCloud|null find($id, $lockMode = null, $lockVersion = null)
 * @method TagCloud|null findOneBy(array $criteria, array $orderBy = null)
 * @method TagCloud[]    findAll()
 * @method TagCloud[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TagCloudRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, TagCloud::class);
    }

    // /**
    //  * @return TagCloud[] Returns an array of TagCloud objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('t.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?TagCloud
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
