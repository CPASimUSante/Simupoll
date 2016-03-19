<?php
namespace CPASimUSante\SimupollBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller as BaseController;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

use CPASimUSante\SimupollBundle\Entity\Simupoll;

use Claroline\CoreBundle\Event\Log\LogResourceReadEvent;
use Claroline\CoreBundle\Library\Resource\ResourceCollection;

class Controller extends BaseController {
    /**
     * @param string $permission
     *
     * @param Simupoll $simupoll
     *
     * @throws AccessDeniedException
     */
    protected function checkAccess($permission, Simupoll $simupoll)
    {
        $collection = new ResourceCollection(array($simupoll->getResourceNode()));
        if (!$this->get('security.authorization_checker')->isGranted($permission, $collection)) {
            throw new AccessDeniedException($collection->getErrorsForDisplay());
        }
    }

    /**
     * @param string $permission
     *
     * @param Simupoll $simupoll
     *
     * @return bool
     */
    protected function isUserGranted($permission, Simupoll $simupoll, $collection = null)
    {
        if ($collection === null) {
            $collection = new ResourceCollection(array($simupoll->getResourceNode()));
        }
        $checkPermission = false;
        if ($this->get('security.authorization_checker')->isGranted($permission, $collection)) {
            $checkPermission = true;
        }
        return $checkPermission;
    }
}
