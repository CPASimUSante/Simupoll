<?php

namespace CPASimUSante\SimupollBundle\Manager;

use JMS\DiExtraBundle\Annotation as DI;
use Doctrine\ORM\EntityManager;
use Claroline\CoreBundle\Persistence\ObjectManager;

use CPASimUSante\SimupollBundle\Entity\Proposition;

/**
 * Helper functions for Proposition
 *
 * @DI\Service("cpasimusante.simupoll.proposition_manager")
 */
class PropositionManager
{
    private $om;

    /**
     * @DI\InjectParams({
     *     "om" = @DI\Inject("claroline.persistence.object_manager")
     * })
     *
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * Deletes a Proposition
     *
     * @param Proposition $proposition
     */
    public function deleteProposition(Proposition $proposition)
    {
        // $this->om->remove($proposition);
        // $this->om->flush();
    }
}
