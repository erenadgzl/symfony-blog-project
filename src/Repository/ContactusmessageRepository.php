<?php

namespace App\Repository;

use App\Entity\Contactusmessage;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Contactusmessage|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contactusmessage|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contactusmessage[]    findAll()
 * @method Contactusmessage[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactusmessageRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Contactusmessage::class);
    }

    // /**
    //  * @return Contactusmessage[] Returns an array of Contactusmessage objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('c.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Contactusmessage
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
