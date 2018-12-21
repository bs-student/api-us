<?php

namespace AppBundle\Repository;

use Doctrine\ORM\EntityRepository;

/**
 * NewsletterRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class NewsletterRepository extends EntityRepository
{
    function getAllNewsletterEmailSearchResult($searchQuery, $pageNumber, $pageSize,$sort){

        $firstResult = ($pageNumber - 1) * $pageSize;
        $qb= $this->getEntityManager()
            ->createQueryBuilder('n')
            ->select('n.id as newsletterId,
                      n.email,
                      n.activationStatus,
                      n.lastUpdateDateTime
            ')
            ->from('AppBundle:Newsletter', 'n')
            ->andwhere('n.email LIKE :query ')
            ->setParameter('query', '%' . $searchQuery . '%')
            ->setMaxResults($pageSize)
            ->setFirstResult($firstResult);

        foreach($sort as  $key => $value){
            $qb->addOrderBy("n.".$key,$value);
        }
        return $qb->getQuery()
            ->getResult();

    }

    public function getAllNewsletterEmailSearchNumber($searchQuery)
    {
        return $this->getEntityManager()
            ->createQueryBuilder('n')
            ->select('COUNT(n)')
            ->from('AppBundle:Newsletter', 'n')
            ->andwhere('n.email LIKE :query ')
            ->setParameter('query', '%' . $searchQuery . '%')
            ->getQuery()
            ->getSingleScalarResult();
    }

    public function getAllEmails(){

        $qb= $this->getEntityManager()
            ->createQueryBuilder('n')
            ->select('n.id as newsletterId,
                      n.email,
                      n.activationStatus,
                      n.lastUpdateDateTime
            ')
            ->from('AppBundle:Newsletter', 'n');

        return $qb->getQuery()
            ->getResult();
    }
}
