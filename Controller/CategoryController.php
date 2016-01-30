<?php
namespace CPASimUSante\SimupollBundle\Controller;

use CPASimUSante\SimupollBundle\Entity\Category;
use CPASimUSante\SimupollBundle\Form\CategoryType;
use CPASimUSante\SimupollBundle\Entity\Simupoll;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as EXT;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
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
    //https://github.com/Atlantic18/DoctrineExtensions/blob/master/doc/transaction-safety.md

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

        $repo = $em->getRepository('CPASimUSanteSimupollBundle:Category');
        $options = array(
            'decorate' => true,
            'rootOpen' => '<ul>',
            'rootClose' => '</ul>',
            'childOpen' => '<li>',
            'childClose' => '</li>',
            'nodeDecorator' => function($node) {
                $add = ' <a class="btn btn-primary btn-sm category-add-btn" data-id="'.$node['id'].'" href="#"><i class="fa fa-plus"></i></a>';
                $delete = ' <a class="btn btn-danger btn-sm category-delete-btn" data-id="'.$node['id'].'" href="#"><i class="fa fa-trash"></i></a>';
                return $node['name'].$add.$delete;
            }
        );
        $htmlTree = $repo->childrenHierarchy(
            null, /* starting from root nodes */
            false, /* true: load all children, false: only direct */
            $options
        );

        return array(
            '_resource' => $simupoll,
            'tree' => $htmlTree,
        );
    }

    /**
     * Data for modal form for category add
     *
     * @EXT\Route(
     *     "/add/form/{idcategory}",
     *     name="cpasimusante_simupoll_category_add_form",
     *     options = {"expose"=true}
     * )
     * @EXT\Template("CPASimUSanteSimupollBundle:Category:addCategory.html.twig")
     */
    public function categoryAddFormAction($idcategory)
    {
        $form = $this->get('form.factory')
            ->create(new CategoryType());
        return array(
            'form' => $form->createView(),
            'parent' => $idcategory
        );
    }

    /**
     * Process category add
     *
     * @EXT\Route(
     *     "/add/{idcategory}",
     *     name="cpasimusante_simupoll_category_add",
     *     options = {"expose"=true}
     * )
     * @EXT\Method("POST")
     * @EXT\Template("CPASimUSanteSimupollBundle:Category:addCategory.html.twig")
     */
    public function categoryAddAction(Request $request, $idcategory)
    {
        $form = $this->get('form.factory')
            ->create(new CategoryType());
        $form->handleRequest($request);

        if ($form->isValid()) {
            $user = $this->container
                ->get('security.token_storage')
                ->getToken()->getUser();

            $em = $this->getDoctrine()->getManager();

            $newcat = $form->getData();
            $newcat->setUser($user);
            if ($idcategory != 0) {
                $category = $em->getRepository('CPASimUSanteSimupollBundle:Category')
                    ->findOneById($idcategory);
            }
            else
            {
                $category = null;
            }
            $newcat->setParent($category);
            $em->persist($newcat);
            $em->flush();
            return new JsonResponse('success', 200);
        }
        else
        {
            return array(
                'form'          => $form->createView(),
                'idcategory'    => $idcategory
            );
        }
    }

    /**
     * Process category delete
     *
     * @EXT\Route(
     *     "/delete/{idcategory}",
     *     name="cpasimusante_simupoll_category_delete_form",
     *     options = {"expose"=true}
     * )
     * @EXT\Template("CPASimUSanteSimupollBundle:Category:deleteCategory.html.twig")
     */
    public function categoryDeleteAction(Request $request, $idcategory)
    {
        if (!is_null($idcategory)) {
            $em = $this->getDoctrine()->getManager();
            $user = $this->container
                ->get('security.token_storage')
                ->getToken()->getUser();
            $category = $em->getRepository('CPASimUSanteSimupollBundle:Category')
                ->findOneBy(
                    array(
                        'id'=>$idcategory,
                        'user'=>$user
                    ));
            $em->remove($category);
            $em->flush();
            return new JsonResponse('success', 200);
        }
        else
        {
            return array();
        }
    }
}