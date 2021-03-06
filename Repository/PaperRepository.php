<?php

namespace CPASimUSante\SimupollBundle\Repository;

/**
 * PaperRepository.
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class PaperRepository extends \Doctrine\ORM\EntityRepository
{
    /**
     * Get paper for current timeframe.
     */
    public function getCurrentPaper($userID, $simupollID, $start, $stop = '')
    {
        $now = new \DateTime();
        $now->setTime(0, 0, 0);
        $qb = $this->createQueryBuilder('p');
        $qb->join('p.user', 'u')
            ->join('p.simupoll', 's')
            ->where('u.id = :userid')
            ->andWhere('s.id = :simupollid')
            ->andWhere('p.start >= :start')
            ->setParameters(
                array(
                    'userid' => $userID,
                    'simupollid' => $simupollID,
                    'start' => $start,
                )
            );

        return $qb->getQuery()->getOneOrNullResult();
        //return $qb->getQuery()->getSQL();
    }

    /**
     * Get unfinished paper for user.
     *
     * @param $userID
     * @param $simupollID
     *
     * @return array
     */
    public function getPaper($userID, $simupollID, $periodId)
    {
        $qb = $this->createQueryBuilder('p');
        $qb->join('p.user', 'u')
            ->join('p.simupoll', 's')
            ->join('p.period', 'd')
            ->where('u.id = :userid')
            ->andWhere('s.id = :simupollid')
            ->andWhere('p.end IS NULL')
            ->andWhere('d.id = :periodId')
            ->setParameters(
                array(
                    'userid' => $userID,
                    'simupollid' => $simupollID,
                    'periodId' => $periodId,
                )
            );

        //echo '<pre>';var_dump($qb->getQuery()->getSQL());echo '</pre>';
        //echo '<pre>';var_dump($qb->getQuery()->getParameters());echo '</pre>';
        return $qb->getQuery()->getResult();
    }

    /**
     * Get paper for current timeframe.
     */
    public function getCurrentPaperInPeriod($userID, $simupollID, $periodId)
    {
        $now = new \DateTime();
        $now->setTime(0, 0, 0);
        $qb = $this->createQueryBuilder('p');
        $qb->join('p.user', 'u')
            ->join('p.simupoll', 's')
            ->join('p.period', 'd')
            ->where('u.id = :userid')
            ->andWhere('s.id = :simupollid')
            ->andWhere('d.id = :periodId')
            ->setParameters(
                array(
                    'userid' => $userID,
                    'simupollid' => $simupollID,
                    'periodId' => $periodId,
                )
            );

        return $qb->getQuery()->getResult();
        //return $qb->getQuery()->getSQL();
    }

    /**
     * @param $userID integer
     * @param $ids array
     *
     * @return array
     */
    public function findPapersByUserAndIds($userID, $ids)
    {
        $qb = $this->createQueryBuilder('p');
        $qb->join('p.user', 'u')
            ->join('p.period', 'd')
            ->where('u.id = :userid')
            ->andWhere('p.id IN (:ids)')
            ->setParameters(
                array(
                    'userid' => $userID,
                    'ids' => $ids,
                )
            );

        return $qb->getQuery()->getResult();
    }

    public function findByUserAndSimupoll($userID, $sid)
    {
        $qb = $this->createQueryBuilder('p');
        $qb->join('p.user', 'u')
            ->join('p.simupoll', 's')
            ->where('u.id = :userid')
            ->andWhere('s.id =:sid')
            ->setParameters(
                array(
                    'userid' => $userID,
                    'sid' => $sid,
                )
            );

        return $qb->getQuery()->getResult();
    }

    /**
     * Get all the users that have answered to this Simupoll.
     *
     * @param $sid simupoll id
     *
     * @return array array of users
     */
    public function getAllUsers($sid)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('DISTINCT IDENTITY(p.user) AS user')
            ->from('CPASimUSante\\SimupollBundle\\Entity\\Paper', 'p')
            ->leftJoin('p.simupoll', 's')
            ->where('s.id = :sid')
            ->setParameter('sid', $sid);
        echo $qb->getQuery()->getSQL().'<br><br>';

        return $qb->getQuery()->getResult();
    }
}
