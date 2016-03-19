<?php

/*
 * This file is part of the Claroline Connect package.
 *
 * (c) Claroline Consortium <consortium@claroline.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CPASimUSante\SimupollBundle\Testing;

use CPASimUSante\SimupollBundle\Entity\Category;
use CPASimUSante\SimupollBundle\Entity\Simupoll;

//to create a resource
use Claroline\CoreBundle\Entity\Resource\ResourceNode;
use Claroline\CoreBundle\Entity\Resource\ResourceType;

use Claroline\CoreBundle\Entity\Role;
use Claroline\CoreBundle\Entity\User;
use Claroline\CoreBundle\Entity\Workspace\Workspace;
use Claroline\CoreBundle\Persistence\ObjectManager;

/**
* Create fully usable entity instance for persistance
*/
class Persister
{
    private $om;

    private $userRole;
    private $simupollType;

    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    public function user($username)
    {
        $user = new User();
        $user->setFirstName($username);
        $user->setLastName($username);
        $user->setUsername($username);
        $user->setPassword($username);
        $user->setMail($username . '@mail.com');
        $user->setGuid($username);
        $this->om->persist($user);

        if (!$this->userRole) {
            $this->userRole = new Role();
            $this->userRole->setName('ROLE_USER');
            $this->userRole->setTranslationKey('user');
            $this->om->persist($this->userRole);
        }

        $user->addRole($this->userRole);
        $workspace = new Workspace();
        $workspace->setName($username);
        $workspace->setCreator($user);
        $workspace->setCode($username);
        $workspace->setGuid($username);
        $this->om->persist($workspace);

        $user->setPersonalWorkspace($workspace);

        return $user;
    }

    public function category($cname, $user, $simupoll)
    {
        $category = new Category();
        $category->setName($cname);
        $category->setUser($user);
        $category->setSimupoll($simupoll);
        $this->om->persist($category);
        return $user;
    }

    /**
    * needed to create a functional simupoll resource
    */
    public function simupoll($title, User $creator)
    {
      $simupoll = new Simupoll();
      $simupoll->setTitle($title);
      if (!$this->simupollType) {
          $this->simupollType = new ResourceType();
          $this->simupollType->setName('claroline_result');
          $this->om->persist($this->simupollType);
      }
      $node = new ResourceNode();
      $node->setName($title);
      $node->setCreator($creator);
      $node->setResourceType($this->simupollType);
      $node->setWorkspace($creator->getPersonalWorkspace());
      $node->setClass('CPASimUSante\SimupollBundle\Entity\Simupoll');
      $node->setGuid(time());
      $simupoll->setResourceNode($node);
      $this->om->persist($simupoll);
      $this->om->persist($node);
      return $simupoll;
    }
}
