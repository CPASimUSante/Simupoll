<?php

namespace CPASimUSante\SimupollBundle\Repository;

use Doctrine\ORM\EntityRepository;
use CPASimUSante\SimupollBundle\Entity\Category;
use CPASimUSante\SimupollBundle\Entity\Question;

class QuestionRepository extends EntityRepository
{
    /**
     * count the number of question associated with a category
     *
     * @param $cid
     * @return mixed
     */
    public function getQuestionCount($cid)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('COUNT(q.id) AS qcount')
            ->from('CPASimUSante\SimupollBundle\Entity\Question', 'q')
            ->where('q.category = :category')
            ->setParameter('category',$cid);
        return $qb->getQuery()->getSingleScalarResult();
    }

    public function getQuestionsWithCategories($sid)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('q.id, q.title, c.name, p.id, p.choice')
            ->from('CPASimUSante\SimupollBundle\Entity\Question', 'q')
            ->leftJoin('CPASimUSante\SimupollBundle\Entity\Category', 'c', 'WITH', 'q.category = c')
            ->join('CPASimUSante\SimupollBundle\Entity\Proposition', 'p')
            ->where('c.simupoll = :simupoll')
            ->orderBy('c.lft', 'ASC')
            ->addOrderBy('q.id', 'ASC')
            ->addOrderBy('p.id', 'ASC')
            ->setParameter('simupoll', $sid);
        return $qb->getQuery()->getResult();
    }
}
