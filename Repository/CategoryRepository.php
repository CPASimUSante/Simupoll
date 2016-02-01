<?php

namespace CPASimUSante\SimupollBundle\Repository;

use Doctrine\ORM\EntityRepository;
use CPASimUSante\SimupollBundle\Entity\Category;
use Gedmo\Tree\Entity\Repository\NestedTreeRepository;

class CategoryRepository extends NestedTreeRepository
{
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
}
