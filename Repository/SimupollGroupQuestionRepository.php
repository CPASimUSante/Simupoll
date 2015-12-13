<?php

namespace CPASimUSante\SimupollBundle\Repository;

use Doctrine\ORM\EntityRepository;
use CPASimUSante\SimupollBundle\Entity\Simupoll;
use CPASimUSante\SimupollBundle\Entity\QuestionGroup;
use CPASimUSante\SimupollBundle\Entity\Question;

class SimupollGroupQuestionRepository extends EntityRepository
{
    /**
     * Number of question for a Simupoll
     *
     * @access public
     *
     * @param integer $id if Simupoll
     *
     * @return mixed
     */
    public function getCountQuestion($id)
    {
        $query = $this->_em->createQuery(
            'SELECT count(eq.question) as nbq
                FROM CPASimUSante\SimupollBundle\Entity\SimupollGroupQuestion eq
                WHERE eq.simupoll = ?1'
        );
        $query->setParameter(1, $id);

        return $query->getSingleResult();
    }
}
