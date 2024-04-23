<?php

namespace App\Repository;

use App\Entity\Favicon;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Favicon>
 *
 * @method Favicon|null find($id, $lockMode = null, $lockVersion = null)
 * @method Favicon|null findOneBy(array $criteria, array $orderBy = null)
 * @method Favicon[]    findAll()
 * @method Favicon[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class FaviconRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Favicon::class);
    }

//    /**
//     * @return Favicon[] Returns an array of Favicon objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('f.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Favicon
//    {
//        return $this->createQueryBuilder('f')
//            ->andWhere('f.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
