<?php

namespace App\Repository;

use Doctrine\ORM\EntityRepository;

class JobRepository extends EntityRepository
{
    /**
     * @return void
     */
    public function removeOldRecords(): void
    {
        $qb = $this->createQueryBuilder('e')
            ->where('e.createdAt < :latestDate')
            ->setParameter('latestDate', (new \DateTime())->format('Y-m-d'));

        $jobs = $qb->getQuery()->execute();
        foreach($jobs as $job) {
            $this->getEntityManager()->remove($job);
        }
    }
}