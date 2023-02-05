<?php

namespace App\Repository;

use App\Entity\Lineaspedidos;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Lineaspedidos>
 *
 * @method Lineaspedidos|null find($id, $lockMode = null, $lockVersion = null)
 * @method Lineaspedidos|null findOneBy(array $criteria, array $orderBy = null)
 * @method Lineaspedidos[]    findAll()
 * @method Lineaspedidos[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class LineaspedidosRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lineaspedidos::class);
    }

    public function save(Lineaspedidos $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Lineaspedidos $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

//    /**
//     * @return Lineaspedidos[] Returns an array of Lineaspedidos objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('l.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Lineaspedidos
//    {
//        return $this->createQueryBuilder('l')
//            ->andWhere('l.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }
}
