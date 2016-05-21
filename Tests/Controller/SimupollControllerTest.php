<?php

namespace CPASimUSante\SimupollBundle\Controller;

use Claroline\CoreBundle\Entity\Role;
use Claroline\CoreBundle\Entity\User;
use Claroline\CoreBundle\Persistence\ObjectManager;
use Claroline\CoreBundle\Library\Testing\TransactionalTestCase;
use Claroline\CoreBundle\Library\Testing\RequestTrait;
use CPASimUSante\SimupollBundle\Entity\Category;
use CPASimUSante\SimupollBundle\Entity\Simupoll;
use CPASimUSante\SimupollBundle\Testing\Persister;

class SimupollControllerTest extends TransactionalTestCase
{
    use RequestTrait;
    /** @var ObjectManager */
    private $om;
    /** @var Persister */
    private $persist;
    /** @var User */
    private $john;
    /** @var User */
    private $jane;
    /** @var User */
    private $admin;

    //standard setup
    protected function setUp()
    {
        //if use of setUp(), get parent setUp()
        parent::setUp();
        $this->om = $this->client->getContainer()->get('claroline.persistence.object_manager');
        $this->persist = new Persister($this->om);
        //create 2 standard users
        $this->john = $this->persist->user('john');
        $this->jane = $this->persist->user('jane');
        //create an admin
        $this->persist->role('ROLE_ADMIN');
        $this->admin = $this->persist->user('admin');
        $this->om->flush();
    }

    public function testUserCanAdministrate()
    {
        $simupoll = $this->persist->simupoll('simupoll1', $this->john);
        $category = $this->persist->category('category1', $this->john, $simupoll);
        $this->om->flush();

        $this->request('GET', "/simupoll/open/{$simupoll->getId()}", $this->jane);
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }
}
