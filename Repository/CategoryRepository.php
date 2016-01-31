<?php

namespace CPASimUSante\SimupollBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

class CategoryRepository extends NestedTreeRepository
{
    public function getMaxLevel($simupoll)
    {
        $qb = $this->_em->createQueryBuilder();
        $qb->select('MAX(c.level) AS maxlevel')
            ->from('Category', 'c');
            /*->where('c.simupoll = ?1')
            ->setParameters(array(1 => $simupoll));*/
        return $qb->getQuery();
    }
}
