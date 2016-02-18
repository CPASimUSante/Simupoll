<?php

namespace CPASimUSante\SimupollBundle\Repository;

use CPASimUSante\SimupollBundle\Entity\Simupoll;
use Doctrine\ORM\EntityRepository;
use CPASimUSante\SimupollBundle\Entity\Period;

class PeriodRepository extends EntityRepository
{
    /**
     * Get currently opened simupoll period
     *
     * @param $simupollID
     * @return array
     */
    public function getOpenedPeriodForSimupoll($simupollID)
    {
        $now = new \DateTime();
        $now->setTime(0, 0, 0);
        $qb = $this->createQueryBuilder('p');
        $qb->join('p.simupoll', 's')
            ->where('s.id = :simupollid')
            ->andWhere('p.start <= :now')
            ->andWhere('p.stop >= :now')
            ->setParameters(
                array(
                    'simupollid' => $simupollID,
                    'now' => $now
                )
            );
        return $qb->getQuery()->getResult();
    }
}
