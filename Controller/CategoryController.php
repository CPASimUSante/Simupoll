<?php
namespace CPASimUSante\SimupollBundle\Controller;

use CPASimUSante\SimupollBundle\Entity\Category;
use CPASimUSante\SimupollBundle\Entity\Simupoll;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as EXT;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class CategoryController
 *
 * @category   Controller
 * @package    CPASimUSante
 * @subpackage Simupoll
 * @author     CPASimUSante <contact@simusante.com>
 * @copyright  2015 CPASimUSante
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 * @version    0.1
 * @link       http://simusante.com
 *
 * @EXT\Route(
 *      name    = "cpasimusante_category",
 * )
 */
class CategoryController extends Controller
{
    /**
     * Lists all Categories.
     *
     * @EXT\Route(
     *      "/categories/{id}",
     *      name="cpasimusante_simupoll_category_manage",
     *      requirements={},
     *      options={"expose"=true}
     * )
     * @EXT\ParamConverter("simupoll", class="CPASimUSanteSimupollBundle:Simupoll", options={"id" = "id"})
     * @EXT\Template("CPASimUSanteSimupollBundle:Category:list.html.twig")
     *
     * @access public
     *
     * @param integer $simupoll id of Simupoll
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction(Simupoll $simupoll)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->container
            ->get('security.token_storage')
            ->getToken()->getUser();

/*
        $cat1 = new Category();
        $cat1->setName('u1.cat1');
        $cat1->setUser($user);

        $cat2 = new Category();
        $cat2->setName('u1.cat2');
        $cat2->setParent($cat1);
        $cat2->setUser($user);

        $cat3 = new Category();
        $cat3->setName('u1.cat3');
        $cat3->setParent($cat1);
        $cat3->setUser($user);

        $cat4 = new Category();
        $cat4->setName('u1.cat4');
        $cat4->setParent($cat2);
        $cat4->setUser($user);

        $em->persist($cat1);
        $em->persist($cat2);
        $em->persist($cat3);
        $em->persist($cat4);
        $em->flush();
*/
        //https://github.com/l3pp4rd/DoctrineExtensions/blob/master/doc/tree.md#create-html-tree
        //http://stackoverflow.com/questions/25090919/create-tree-nested-select-option
        $repo = $em->getRepository('CPASimUSanteSimupollBundle:Category');
        $options = array(
            'decorate' => true,
            'rootOpen' => '<ul>',
            'rootClose' => '</ul>',
            'childOpen' => '<li>',
            'childClose' => '</li>'/*,
            'nodeDecorator' => function($node) {
                return '<a href="/page/'.$node['slug'].'">'.$node[$field].'</a>';
            }*/
        );
        $htmlTree = $repo->childrenHierarchy(
            null, /* starting from root nodes */
            false, /* true: load all children, false: only direct */
            $options
        );

        return array(
            '_resource' => $simupoll,
            'tree' => $htmlTree
        );
    }
}