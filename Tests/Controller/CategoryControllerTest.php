<?php

namespace CPASimUSante\SimupollBundle\Controller;

use CPASimUSante\SimupollBundle\Entity\Category;
use CPASimUSante\SimupollBundle\Entity\Simupoll;
use Claroline\CoreBundle\Entity\User;
use Claroline\CoreBundle\Persistence\ObjectManager;
use CPASimUSante\SimupollBundle\Testing\Persister;
use Claroline\CoreBundle\Library\Testing\TransactionalTestCase;
use Claroline\CoreBundle\Library\Testing\RequestTrait;

class CategoryControllerTest extends TransactionalTestCase
{
    use RequestTrait;
    /** @var ObjectManager */
    private $om;
    /** @var Persister */
    private $persist;

    //standard setup
    protected function setUp()
    {
        //if use of setUp(), get parent setUp()
        parent::setUp();
        $this->om = $this->client->getContainer()->get('claroline.persistence.object_manager');
        $this->persist = new Persister($this->om);
    }

    public function testCategoryFound()
    {
        $john = $this->persist->user('john');
        $simupoll = $this->persist->simupoll('simupoll1', $john);
        $category = $this->persist->category('category1', $john, $simupoll);
        $this->om->flush();

        $this->request('GET', "/simupoll/category/categories/{$simupoll->getId()}");
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }

    public function testDeleteCategoryNotAllowed()
    {
        $john = $this->persist->user('john');
        $jane = $this->persist->user('jane');
        $simupoll = $this->persist->simupoll('simupoll1', $john);
        $category = $this->persist->category('category1', $john, $simupoll);
        $this->om->flush();

        $this->request('DELETE', "/simupoll/category/delete/{$category->getId()}", $jane);
        $this->assertEquals(403, $this->client->getResponse()->getStatusCode());
    }
}
