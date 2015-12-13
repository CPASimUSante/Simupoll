<?php
namespace CPASimUSante\SimupollBundle\Services;
use Claroline\CoreBundle\Library\Resource\ResourceCollection;
class SimupollServices
{
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