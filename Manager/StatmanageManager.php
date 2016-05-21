<?php

namespace CPASimUSante\SimupollBundle\Manager;

use JMS\DiExtraBundle\Annotation as DI;
use Claroline\CoreBundle\Persistence\ObjectManager;
use CPASimUSante\SimupollBundle\Entity\Simupoll;

/**
 * Helper functions for Statmanage.
 *
 * @DI\Service("cpasimusante.simupoll.statmanage_manager")
 */
class StatmanageManager
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

    public function getStatmanageBySimupollAndUser($user, $simupoll)
    {
        return $this->om->getRepository('CPASimUSanteSimupollBundle:Statmanage')
            ->findBy(array('user' => $user, 'simupoll' => $simupoll));
    }
}
