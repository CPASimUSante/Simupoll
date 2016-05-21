<?php

namespace CPASimUSante\SimupollBundle\Manager;

use JMS\DiExtraBundle\Annotation as DI;
use Claroline\CoreBundle\Persistence\ObjectManager;
use CPASimUSante\SimupollBundle\Entity\Simupoll;

/**
 * Helper functions for Statcategorygroup.
 *
 * @DI\Service("cpasimusante.simupoll.statcategorygroup_manager")
 */
class StatcategorygroupManager
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

    public function getStatcategorygroupByStatmanage($sm)
    {
        return $this->om->getRepository('CPASimUSanteSimupollBundle:Statcategorygroup')
            ->findBy(array('statmanage' => $sm));
    }
}
