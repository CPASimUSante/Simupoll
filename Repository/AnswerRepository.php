<?php

namespace CPASimUSante\SimupollBundle\Repository;

/**
 * ResponseRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class AnswerRepository extends \Doctrine\ORM\EntityRepository
{
    public function getSimupollAllResponsesForAllUsersQuery($simupollId, $order)
    {
        return $this->getQuerySimupollAllResponsesForAllUsers($simupollId, $order)->getResult();
    }

    /**
     *
     * @return array
     */
    public function getQuerySimupollAllResponsesForAllUsers($simupollId, $order)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('a')
            ->from('CPASimUSante\\SimupollBundle\\Entity\\Answer', 'a')
            ->join('a.paper', 'p')
            ->join('p.simupoll', 's')
            ->where('s.id = :sid')
//            ->orderBy($order, 'ASC')
            ->setParameters(array('sid' => $simupollId));
        return $qb->getQuery();
    }

    /**
     * Retrieve answers for question in
     * @param $sid
     * @param $pid
     * @param $current
     * @param $next
     * @return array
     */
    public function getAnswersForQuestions($sid, $pid, $current=-1, $next=-1)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('a.id, q.id as qid, a.answer')
            ->from('CPASimUSante\SimupollBundle\Entity\Answer', 'a')
            ->leftJoin('a.question', 'q')
            ->leftJoin('q.category', 'c')
            ->where('q.simupoll = :simupoll');
        if ($pid != 0) {
            $qb->andWhere('a.paper = :paper');
            $qb->setParameter('paper', $pid);
        }
        if ($current != -1) {
            $qb->andWhere('c.lft >=:current');
            $qb->setParameter('current', $current);
        }
        if ($next != -1) {
            $qb->andWhere('c.lft <=:next');
            $qb->setParameter('next', $next);
        }
        $qb->orderBy('q.id', 'ASC');
        $qb->setParameter('simupoll', $sid);
        //echo $qb->getQuery()->getSQL();
        return $qb->getQuery()->getResult();
    }

    /**
     * Delete answers when updating a simupoll
     *
     * @param $pid
     * @param $questionList
     */
    public function deleteOldAnswersInCategories($pid, $questionList)
    {
        $qb = $this->_em->createQueryBuilder();
        //$query = $qb->delete('CPASimUSante\SimupollBundle\Entity\Answer', 'a')
        $query = $qb->delete('CPASimUSanteSimupollBundle:Answer', 'a')
            ->where('a.question IN (:questionslist)')
            ->andWhere('a.paper = :pid')
            ->setParameter('pid', $pid)
            ->setParameter('questionslist', $questionList)
            ->getQuery();
        $query->execute();
    }

    public function getAverageForExerciseByUser($sid)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('AVG(r.mark) as average_mark')
            ->addSelect('IDENTITY(p.user) as user')             //IDENTITY needed because user is a FK
            ->from('CPASimUSante\\SimupollBundle\\Entity\\Answer', 'a')     //thus, avoid problem with "overriding" Response entity in ExoverrideBundle
            ->join('a.paper', 'p')
            ->join('p.simupoll', 'e')
            ->where('e.id = ?1')
            ->groupBy('p.user')
            ->setParameters(array(1 => $simupollId));
        return $qb->getQuery()->getResult();
    }

    /**
    * @param $sid simupoll id
    */
    public function getAverageForSimupollLastTryByUser($sid)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('AVG(a.mark) as average_mark')
            ->addSelect('IDENTITY(p.user) as user')             //IDENTITY needed because user is a FK
            ->from('CPASimUSante\\SimupollBundle\\Entity\\Answer', 'a')     //thus, avoid problem with "overriding" Response entity in ExoverrideBundle
            ->join('a.paper', 'p')
            ->join('p.simupoll', 's')
            ->where('s.id = ?1')
            ->andWhere(
               $qb->expr()->in(
                   'p.id',
                   $this->_em->createQueryBuilder()->select('MAX(p2.id)')
                       ->from('CPASimUSante\\SimupollBundle\\Entity\\Paper', 'p2')
                       ->where('p2.simupoll= ?1')
                       ->groupBy('p2.user')
                       ->getDQL()
               ))
            ->groupBy('p.user')
            ->setParameters(array(1 => $sid));
        return $qb->getQuery()->getResult();
    }
}
