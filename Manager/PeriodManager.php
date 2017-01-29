<?php

namespace CPASimUSante\SimupollBundle\Manager;

use JMS\DiExtraBundle\Annotation as DI;
use Claroline\CoreBundle\Persistence\ObjectManager;
use CPASimUSante\SimupollBundle\Entity\Simupoll;
use CPASimUSante\SimupollBundle\Entity\Period;

/**
 * Helper functions for periods.
 *
 * @DI\Service("cpasimusante.simupoll.period_manager")
 */
class PeriodManager
{
    private $om;
    private $simupollManager;

     /**
      * @DI\InjectParams({
      *     "om"                 = @DI\Inject("claroline.persistence.object_manager"),
      *     "simupollManager"    = @DI\Inject("cpasimusante.simupoll.simupoll_manager")
      * })
      *
      * @param ObjectManager $om
      * @param SimupollManager   simupollManager
      */
     public function __construct(
         ObjectManager $om,
         SimupollManager $simupollManager
         ) {
         $this->om = $om;
         $this->simupollManager = $simupollManager;
     }

    public function getPeriodBySimupollAndId($idperiod, $sid)
    {
        return $this->om->getRepository('CPASimUSanteSimupollBundle:Period')
            ->findOneBy(
            [
                'id' => $idperiod,
                'simupoll' => $sid,
            ]);
    }

    public function getPeriodBySimupoll(Simupoll $simupoll)
    {
        $query = $this->om->createQueryBuilder()
            ->select('p')
            ->from('CPASimUSante\SimupollBundle\Entity\Period', 'p')
            ->where('p.simupoll = ?1')
            ->setParameters([1 => $simupoll])
            ->getQuery();
        $rawPeriods = $query->getArrayResult();
        $periods = [];
        foreach ($rawPeriods as $rawPeriod) {
            //to have the correct format : 2017-01-22T23:00:00.000Z, instead of Datetime object to be used in datetimepicker
            $start = (isset($rawPeriod['start'])) ? $rawPeriod['start']->format('Y-m-d\TH:i:s\Z') : '';
            $stop = (isset($rawPeriod['stop'])) ? $rawPeriod['stop']->format('Y-m-d\TH:i:s\Z') : '';

            $periods[] = [
                'id'=>$rawPeriod['id'],
                'start'=>$start,
                'stop'=>$stop,
                'title'=>$rawPeriod['title'],
            ];
        }
        return $periods;
    }

    /**
     * @return bool
     */
    public function getOpenedPeriod($sid)
    {
        $isOpenedPeriod = $this->om->getRepository('CPASimUSanteSimupollBundle:Period')
            ->isOpenedPeriodForSimupoll($sid);
        $opened = ($isOpenedPeriod > 0) ? true : false;

        return $opened;
    }

    /**
     * @param int $sid Simipoll id
     *
     * @return array list of periods array('id', 'title', 'entity')
     */
    public function getPeriods($sid)
    {
        $now = new \DateTime();
        $now->setTime(0, 0, 0);

        $periods = [];
        $period_list = $this->om->getRepository('CPASimUSanteSimupollBundle:Period')
            ->findBySimupoll($sid);
        foreach ($period_list as $period) {
            $periods['id'][] = $period->getId();
            $periods['title'][] = $period->getTitle();
            $periods['entity'][] = $period;

            if (($period->getStart()->format('Y-m-d') <= $now->format('Y-m-d')) &&
                ($period->getStop()->format('Y-m-d') >= $now->format('Y-m-d'))) {
                $periods['current'][] = true;
            } else {
                $periods['current'][] = false;
            }
        }

        return $periods;
    }

    /**
     * Deletes a period.
     *
     * @param int $pid
     * @param int $sid
     */
    public function deletePeriod($pid, $sid)
    {
        $period = $this->getPeriodBySimupollAndId($pid, $sid);
        $this->om->remove($period);
        $this->om->flush();
    }

    /**
     * Adds a period.
     *
     * @param int    $sid
     * @param string $categoryName
     */
    public function addPeriod($sid, $periodTitle, $periodStart, $periodStop)
    {
        $simupoll = $this->simupollManager->getSimupollById($sid);
        $newPeriod = new Period();
        $newPeriod->setTitle($periodTitle);
        $newPeriod->setStart(new \DateTime($periodStart));
        $newPeriod->setStop(new \DateTime($periodStop));

        $newPeriod->setTimezone(date_default_timezone_get());
        //Add simupoll info
        $newPeriod->setSimupoll($simupoll);
        $this->om->persist($newPeriod);
        $this->om->flush();

        return $newPeriod;
    }

    /**
     * Update a period.
     *
     * @param int    $pid
     * @param int    $sid
     * @param string $periodTitle
     * @param string $periodStart
     * @param string $periodStop
     */
    public function updatePeriod(Period $period, $periodTitle, $periodStart, $periodStop)
    {
        $period->setTitle($periodTitle);
        $period->setStart(new \DateTime($periodStart));
        $period->setStop(new \DateTime($periodStop));
        $this->om->persist($period);
        $this->om->flush();
    }
}
