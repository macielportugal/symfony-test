<?php

namespace App\Repository;

use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method User|null find($id, $lockMode = null, $lockVersion = null)
 * @method User|null findOneBy(array $criteria, array $orderBy = null)
 * @method User[]    findAll()
 * @method User[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, User::class);
    }

    public function getItems($request)
    {
        $query = $this->createQueryBuilder('p')->select('p.id', 'p.email', 'p.firstName', 'p.lastName', 'p.roles');

        if ($request->query->get('firstName') !== null) {
            $query = $query
                        ->andWhere('lower(p.firstName) like lower(:firstName)')
                        ->setParameter('firstName', $request->query->get('firstName').'%');
        }

        if ($request->query->get('lastName') !== null) {
            $query = $query
                        ->andWhere('lower(p.lastName) like lower(:lastName)')
                        ->setParameter('lastName', $request->query->get('lastName').'%');
        }

        if ($request->query->get('email') !== null) {
            $query = $query
                        ->andWhere('lower(p.email) like lower(:email)')
                        ->setParameter('email', $request->query->get('email').'%');
        }

        return $query->orderBy('p.id')->getQuery();
    }

    // /**
    //  * @return User[] Returns an array of User objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('u.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?User
    {
        return $this->createQueryBuilder('u')
            ->andWhere('u.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
