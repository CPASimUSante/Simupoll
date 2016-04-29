<?php

namespace CPASimUSante\SimupollBundle\Repository;

use Doctrine\ORM\EntityRepository;
use CPASimUSante\SimupollBundle\Entity\Simupoll;

class SimupollRepository extends EntityRepository
{
    public function getQuestionsAndPropositions($sid)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('s, q, p')
            ->from('CPASimUSante\SimupollBundle\Entity\Simupoll', 's')
            ->leftJoin('CPASimUSante\SimupollBundle\Entity\Question', 'q', 'WITH', 'q.simupoll = s')
            ->leftJoin('CPASimUSante\SimupollBundle\Entity\Proposition', 'p', 'WITH', 'p.question = q')
            ->where('s.id= :sid')
            ->setParameter('sid',$sid);
            $sql = $qb->getQuery()->getSQL();
        //return $qb->getQuery()->getResult();
        echo '<pre>';var_dump($sql);echo '</pre>';
        die();
    }
}
