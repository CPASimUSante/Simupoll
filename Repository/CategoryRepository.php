<?php

namespace CPASimUSante\SimupollBundle\Repository;

use Doctrine\ORM\EntityRepository;
use CPASimUSante\SimupollBundle\Entity\Category;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

class CategoryRepository extends NestedTreeRepository
{
    /**
     * @param $simupoll
     * @return mixed
     * @throws \Doctrine\ORM\NonUniqueResultException
     */
    public function getMaxLevel($simupoll)
    {
        $dql = '
            SELECT MAX(c.lvl) AS maxlevel FROM CPASimUSante\SimupollBundle\Entity\Category c
            WHERE c.id >0
        ';
        $query = $this->_em->createQuery($dql);
        //$query->setParameter('simupoll', $id);
        return $query->getOneOrNullResult();
    }

    /**
     * get root category for simupoll
     *
     */
    public function getRoot($sid)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('partial c.{id}')
            ->from('CPASimUSante\SimupollBundle\Entity\Category', 'c')
            ->where('c.lvl = 0')
            ->andWhere('c.simupoll = :simupoll');
        $qb->setParameter('simupoll', $sid);
        return $qb->getQuery()->getResult();
    }


    /**
     * get root category for simupoll
     *
     */
    public function getCategoriesBetween($sid, $begin=-1, $end=-1)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('c')
            ->from('CPASimUSante\SimupollBundle\Entity\Category', 'c')
            ->where('c.simupoll = :simupoll');
        if ($begin != -1) {
            $qb->andWhere('c.lft >=:begin');
            $qb->setParameter('begin', $begin);
        }
        if ($end != -1) {
            $qb->andWhere('c.lft <=:end');
            $qb->setParameter('end', $end);
        }
        $qb->setParameter('simupoll', $sid);
        return $qb->getQuery()->getResult();
    }

}
