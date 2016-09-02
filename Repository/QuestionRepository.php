<?php

namespace CPASimUSante\SimupollBundle\Repository;

use Doctrine\ORM\EntityRepository;
use CPASimUSante\SimupollBundle\Entity\Category;
use CPASimUSante\SimupollBundle\Entity\Question;

class QuestionRepository extends EntityRepository
{
    /**
     * Count the number of question associated with a category.
     *
     * @param $cid
     *
     * @return mixed
     */
    public function getQuestionCount($cid)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('COUNT(q.id) AS qcount')
            ->from('CPASimUSante\SimupollBundle\Entity\Question', 'q')
            ->where('q.category = :category')
            ->setParameter('category', $cid);

        return $qb->getQuery()->getSingleScalarResult();
    }

    /**
     * Retrieve the list of question for a given simupoll, between 2 categories, defined by lft values.
     *
     * @param $sid simupoll id
     * @param $current integer begin bound for the category (lft value)
     * @param $next integer end bound for the category (lft value)
     *
     * @return array of results
     */
    public function getQuestionsWithinCategories($sid, $current = -1, $next = -1)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('q.id')
            ->from('CPASimUSante\SimupollBundle\Entity\Question', 'q')
            ->leftJoin('q.category', 'c')
            ->where('c.simupoll = :simupoll');
        if ($current != -1) {
            $qb->andWhere('c.lft >=:current');
            $qb->setParameter('current', $current);
        }
        if ($next != -1) {
            $qb->andWhere('c.lft <:next');
            $qb->setParameter('next', $next);
        }
        $qb->orderBy('c.lft', 'ASC')
            ->addOrderBy('q.id', 'ASC')
            ->setParameter('simupoll', $sid);

        return $qb->getQuery()->getArrayResult();
    }

    /**
     * Find questions in the selected categories.
     *
     * @param $sid
     * @param int $current
     * @param int $next
     *
     * @return array of object or null
     */
    public function getQuestionsWithCategories($sid, $current = -1, $next = -1)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('q')
            ->from('CPASimUSante\SimupollBundle\Entity\Question', 'q')
            ->leftJoin('CPASimUSante\SimupollBundle\Entity\Category', 'c', 'WITH', 'q.category = c')
            ->leftJoin('CPASimUSante\SimupollBundle\Entity\Proposition', 'p')
            ->where('c.simupoll =:simupoll');
        if ($current != -1) {
            $qb->andWhere('c.lft >=:current');
            $qb->setParameter('current', $current);
        }
        if ($next != -1) {
            $qb->andWhere('c.lft <:next');
            $qb->setParameter('next', $next);
        }
        $qb->orderBy('c.lft', 'ASC')
            ->addOrderBy('q.id', 'ASC')
            ->addOrderBy('p.id', 'ASC')
            ->setParameter('simupoll', $sid);

        return $qb->getQuery()->getResult();
    }

    /**
     * Retrieve question already answered.
     *
     * @param $sid integer Simupoll id
     * @param $pid integer Paper id
     * @param $limit integer number of question to display
     * @param $offset integer offset of questions to start to
     *
     * @return array list of questions, with their answers
     */
    public function getQuestionsWithAnswers($sid, $pid, $limit = 0, $offset = 0)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('q');
        if ($pid != 0) {
            $qb->addSelect('a.id, a.answer');
        }
        $qb->from('CPASimUSante\SimupollBundle\Entity\Question', 'q');
        if ($pid != 0) {
            $qb->leftJoin('CPASimUSante\SimupollBundle\Entity\Answer', 'a', 'WITH', 'a.question = q');
        }
        $qb->where('q.simupoll = :simupoll');
        if ($pid != 0) {
            $qb->andWhere('a.paper = :paper');
            $qb->setParameter('paper', $pid);
        }
        $qb->orderBy('q.id', 'ASC');
        if ($limit != 0) {
            $qb->setMaxResults($limit);
        }
        if ($offset != 0) {
            $qb->setFirstResult($offset);
        }
        $qb->setParameter('simupoll', $sid);
        //echo $qb->getQuery()->getSQL();
        return $qb->getQuery()->getResult();
    }

    /**
     * @param $sid
     * @param $pid
     * @param $current
     * @param $next
     *
     * @return array
     */
    public function getQuestionsWithAnswersInCategories($sid, $pid, $current, $next)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('q');
        if ($pid != 0) {
            $qb->addSelect('a.id, a.answer');
        }
        $qb->from('CPASimUSante\SimupollBundle\Entity\Question', 'q');
        if ($pid != 0) {
            $qb->leftJoin('CPASimUSante\SimupollBundle\Entity\Answer', 'a', 'WITH', 'a.question = q');
        }
        $qb->leftJoin('CPASimUSante\SimupollBundle\Entity\Category', 'c', 'WITH', 'q.category = c')
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
            $qb->andWhere('c.lft <:next');
            $qb->setParameter('next', $next);
        }
        $qb->orderBy('q.id', 'ASC');
        $qb->setParameter('simupoll', $sid);
        //die($qb->getQuery()->getSQL());
        //return $qb->getQuery()->getSQL();
        return $qb->getQuery()->getResult();
    }
}
