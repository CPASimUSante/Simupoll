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
     * @param $sid
     * @return array
     */
    public function getOpenedPeriodForSimupoll($sid)
    {
        $now = new \DateTime();
        $now->setTime(0, 0, 0);
        $qb = $this->_em->createQueryBuilder();
        $qb->select('p')
            ->from('CPASimUSante\SimupollBundle\Entity\Period', 'p')
            ->join('p.simupoll', 's')
            ->where('s.id = :sid')
            ->andWhere('p.start <= :now')
            ->andWhere('p.stop >= :now')
            ->setParameters(array('sid' => $sid, 'now' => $now));
        return $qb->getQuery()->getResult();
    }

    public function isOpenedPeriodForSimupoll($sid)
    {
        $now = new \DateTime();
        $now->setTime(0, 0, 0);
        $qb = $this->_em->createQueryBuilder();
        $qb->select('COUNT(p.id) AS pcount')
            ->from('CPASimUSante\SimupollBundle\Entity\Period', 'p')
            ->join('p.simupoll', 's')
            ->where('s.id = :sid')
            ->andWhere('p.start <= :now')
            ->andWhere('p.stop >= :now')
            ->setParameters(array('sid' => $sid,'now' => $now));

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     *
     * @param $uid
     * @param $date
     * @return array
     */
    public function findOneContaining($uid, $date)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('p')
            ->from('CPASimUSante\SimupollBundle\Entity\Period', 'p')
            ->join('p.user', 'u')
            ->where('u.id = :uid')
            ->andWhere('p.start <= :thedate')
            ->andWhere('p.stop >= :thedate')
            ->setParameters(array('uid' => $uid, 'thedate' => $date));
        return $qb->getQuery()->getResult();
    }
}
