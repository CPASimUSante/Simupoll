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
     * Categories between lft values
     * @param $sid
     * @param int $begin
     * @param int $end
     * @return array
     */
    public function getCategoriesBetweenLft($sid, $begin=-1, $end=-1)
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
            $qb->andWhere('c.lft <:end');
            $qb->setParameter('end', $end);
        }
        $qb->setParameter('simupoll', $sid);
        //echo $qb->getQuery()->getSQL();
        return $qb->getQuery()->getResult();
    }

    /**
     * List of category from lvl list
     *
     * @param $sid
     * @param $lftList array
     * @return array
     */
    public function getCategoriesInLft($sid, $lftList)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('c')
            ->from('CPASimUSante\SimupollBundle\Entity\Category', 'c')
            ->where('c.simupoll = :simupoll')
            ->andWhere('c.lft IN (:lftlist)')
            ->setParameter('simupoll', $sid)
            ->setParameter('lftlist', $lftList);
        return $qb->getQuery()->getResult();
    }

    public function findLftById($sid, $cids)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('c.lft')
            ->from('CPASimUSante\SimupollBundle\Entity\Category', 'c')
            ->where('c.simupoll = :sid')
            ->andWhere('c.id IN (:categories)')
            ->setParameters(array('sid'=>$sid, 'categories'=>$cids))
            ->orderBy('c.lft', 'ASC');
        $result = $qb->getQuery()->getArrayResult();
        return array_column($result, "lft");
    }

    /**
     * Categories between lft values
     * @param $sid
     * @param int $begin
     * @param int $end
     * @return array
     */
    public function getCategoriesBetween($sid, $begin=-1, $end='')
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('c.id')
            ->from('CPASimUSante\SimupollBundle\Entity\Category', 'c')
            ->where('c.simupoll = ?1')
            ->andWhere('c.lft >= ?2')
            ->setParameter(2, $begin);
        if ($end != '') {
            $qb->andWhere('c.lft < ?3');
            $qb->setParameter(3, $end);
        }
        $qb->setParameter(1, $sid);
        //echo $qb->getQuery()->getSQL();
        $result = $qb->getQuery()->getResult();
        return array_column($result, "id");
    }
}
