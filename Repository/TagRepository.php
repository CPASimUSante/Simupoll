<?php

namespace CPASimUSante\SimupollBundle\Repository;

use Doctrine\ORM\EntityRepository;
use CPASimUSante\SimupollBundle\Entity\Tag;
use Claroline\CoreBundle\Entity\User;

class TagRepository extends EntityRepository
{
    public function countByUserAndId($user, $id)
    {
        $qb = $this->_em->createQueryBuilder('tag');
        $qb->select('COUNT(tag.id)')
            ->where('tag.id = ?1')
            ->andWhere('tag.user =  ?2')
            ->setParameters(array(1 => $id, 2 => $user));
        return $qb->getQuery()->getSingleScalarResult();
    }
}
