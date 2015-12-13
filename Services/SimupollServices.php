<?php
namespace CPASimUSante\SimupollBundle\Services;

use Claroline\CoreBundle\Library\Resource\ResourceCollection;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class SimupollServices
{

    /**
     * Current entity manage for data persist
     * @var \Doctrine\Common\Persistence\ObjectManager $om
     */
    protected $om;

    /**
     * Security Authorization
     * @var \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface $securityAuth
     */
    protected $securityAuth;

    /**
     * Class constructor - Inject required services
     * @param \Doctrine\Common\Persistence\ObjectManager                                          $objectManager
     * @param \Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface        $securityAuth
     */
    public function __construct(
        ObjectManager                 $objectManager,
        AuthorizationCheckerInterface $securityAuth)
    {
        $this->om              = $objectManager;
        $this->securityAuth    = $securityAuth;
    }

    /**
     * To know if an user is the creator of Simupoll
     *
     * @access public
     *
     * @param \CPASimUSante\SimupollBundle\Entity\Simupoll $simupoll
     *
     * @return boolean
     */
    public function isSimupollAdmin($simupoll)
    {
        $collection = new ResourceCollection(array($simupoll->getResourceNode()));
        if ($this->securityAuth->isGranted('ADMINISTRATE', $collection)) {
            return true;
        } else {
            return false;
        }
    }
}