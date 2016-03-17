<?php
namespace CPASimUSante\SimupollBundle\Controller;

use CPASimUSante\SimupollBundle\Entity\Category;
use CPASimUSante\SimupollBundle\Entity\Simupoll;

use Claroline\CoreBundle\Entity\Role;
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

    protected function setUp()
    {
       //if use of setUp(), get parent setUp()
        parent::setUp();
        $this->om = $this->client->getContainer()->get('claroline.persistence.object_manager');
        $this->persist = new Persister($this->om);
    }

    public function testCategoryNotFoundAction()
    {
        $this->request('GET', '/simupoll/category/categories/1');
        $this->assertEquals(404, $this->client->getResponse()->getStatusCode());
    }

    public function testCategoryFoundAction()
    {
        $user = $this->persist->user('john');
        $simupoll = $this->persist->simupoll('simupoll1');
        $category = $this->persist->category('category1', $user, $simupoll);
        $this->om->flush();

        $this->request('GET', "/simupoll/category/categories/{$simupoll->getId()}");
        var_dump($this->client->getResponse()->getContent());
        die();
        $this->assertEquals(200, $this->client->getResponse()->getStatusCode());
    }
}
