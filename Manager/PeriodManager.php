<?php

namespace CPASimUSante\SimupollBundle\Manager;

use JMS\DiExtraBundle\Annotation as DI;
use Doctrine\ORM\EntityManager;
use Claroline\CoreBundle\Persistence\ObjectManager;

use CPASimUSante\SimupollBundle\Entity\Simupoll;

/**
 * Helper functions for periods
 *
 * @DI\Service("cpasimusante.simupoll.period_manager")
 */
class PeriodManager
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

    public function getPeriodBySimupollAndId($idperiod, $sid)
    {
        return $this->om->getRepository('CPASimUSanteSimupollBundle:Period')
            ->findOneBy(
            array(
                'id' => $idperiod,
                'simupoll' => $sid
            ));
    }

    public function getPeriodBySimupoll(Simupoll $simupoll)
    {
        $query = $this->om->createQueryBuilder()
            ->select('p')
            ->from('CPASimUSante\SimupollBundle\Entity\Period', 'p')
            ->where('p.simupoll = ?1')
            ->setParameters(array(1 => $simupoll))
            ->getQuery();
        return $query->getArrayResult();
    }

    /**
    *
    * @return boolean
    */
    public function getOpenedPeriod($sid)
    {
        $isOpenedPeriod = $this->om->getRepository('CPASimUSanteSimupollBundle:Period')
            ->isOpenedPeriodForSimupoll($sid);
        $opened = ($isOpenedPeriod > 0) ? true : false;
        return $opened;
    }

    /**
     * @param integer $sid Simipoll id
     * @return array list of periods array('id', 'title', 'entity')
     */
    public function getPeriods($sid)
    {
        $now = new \DateTime();
        $now->setTime(0, 0, 0);

        $periods = array();
        $period_list = $this->om->getRepository('CPASimUSanteSimupollBundle:Period')
            ->findBySimupoll($sid);
        foreach ($period_list as $period) {
           $periods['id'][]     = $period->getId();
           $periods['title'][]  = $period->getTitle();
           $periods['entity'][] = $period;

           if (($period->getStart()->format("Y-m-d") <= $now->format("Y-m-d")) &&
                ($period->getStop()->format("Y-m-d") >= $now->format("Y-m-d"))) {
                $periods['current'][] = true;
            } else {
                $periods['current'][] = false;
            }
       }
       return $periods;
    }
}
