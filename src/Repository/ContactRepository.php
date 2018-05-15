<?php

namespace App\Repository;

use App\Entity\Activity;
use App\Entity\Contact;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Symfony\Bridge\Doctrine\RegistryInterface;

/**
 * @method Contact|null find($id, $lockMode = null, $lockVersion = null)
 * @method Contact|null findOneBy(array $criteria, array $orderBy = null)
 * @method Contact[]    findAll()
 * @method Contact[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ContactRepository extends ServiceEntityRepository
{
    public function __construct(RegistryInterface $registry)
    {
        parent::__construct($registry, Contact::class);
    }

    public function calculateLatestActivityAt(Contact $contact): ?array
    {
        $builder = $this->getEntityManager()->createQueryBuilder();
        return $builder->select('a.createdAt')
            ->from(Activity::class, 'a')
            ->where($builder->expr()->eq('a.contact', ':contact'))
            ->setParameter('contact', $contact)
            ->orderBy('a.createdAt', 'DESC')
            ->setMaxResults(1)
            ->getQuery()
            ->getOneOrNullResult();
    }

    public function getActivityCount(Contact $contact): string
    {
        $builder = $this->getEntityManager()->createQueryBuilder();
        return $builder->select('count(a.id)')
            ->from(Activity::class, 'a')
            ->where($builder->expr()->eq('a.contact', ':contact'))
            ->setParameter('contact', $contact)
            ->getQuery()
            ->getSingleScalarResult();
    }
}
