<?php

namespace CPASimUSante\SimupollBundle\Services;

use Claroline\CoreBundle\Library\Resource\ResourceCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class SimupollServices
{
    /**
     * Current entity manage for data persist.
     *
     * @var \Doctrine\Common\Persistence\ObjectManager
     */
    protected $om;

    /**
     * Security Authorization.
     *
     * @var \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface
     */
    protected $securityAuth;

    /**
     * Class constructor - Inject required services.
     *
     * @param \Doctrine\Common\Persistence\ObjectManager                                   $objectManager
     * @param \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface $securityAuth
     */
    public function __construct(
        ObjectManager                 $objectManager,
        AuthorizationCheckerInterface $securityAuth)
    {
        $this->om = $objectManager;
        $this->securityAuth = $securityAuth;
    }

    /**
     * To know if an user is the creator of Simupoll.
     *
     *
     * @param \CPASimUSante\SimupollBundle\Entity\Simupoll $simupoll
     *
     * @return bool
     */
    public function isGrantedAccess($simupoll, $access)
    {
        $collection = new ResourceCollection(array($simupoll->getResourceNode()));
        if ($this->securityAuth->isGranted($access, $collection)) {
            return true;
        } else {
            return false;
        }
    }

    public function periodList($simupoll)
    {
        return $this->om->getRepository('CPASimUSanteSimupollBundle:Period')
            ->findBySimupoll($simupoll);
    }
}
