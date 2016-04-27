<?php
namespace CPASimUSante\SimupollBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration as EXT;
use JMS\DiExtraBundle\Annotation as DI;

use CPASimUSante\SimupollBundle\Manager\SimupollManager;
use CPASimUSante\SimupollBundle\Manager\CategoryManager;
use CPASimUSante\SimupollBundle\Entity\Category;
use CPASimUSante\SimupollBundle\Form\CategoryType;
use CPASimUSante\SimupollBundle\Entity\Simupoll;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

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
    private $simupollManager;
    private $categoryManager;
    private $tokenStorage;

    /**
     * @DI\InjectParams({
     *     "simupollManager"  = @DI\Inject("cpasimusante.simupoll.simupoll_manager"),
     *     "categoryManager"  = @DI\Inject("cpasimusante.simupoll.category_manager"),
     *     "tokenStorage"     = @DI\Inject("security.token_storage")
     * })
     *
     * @param SimupollManager   simupollManager
     * @param CategoryManager   categoryManager
     * @param TokenStorageInterface   tokenStorage
     */
    public function __construct(
        SimupollManager $simupollManager,
        CategoryManager $categoryManager,
        TokenStorageInterface $tokenStorage
    )
    {
      $this->simupollManager = $simupollManager;
      $this->categoryManager = $categoryManager;
      $this->tokenStorage    = $tokenStorage;
    }

    /**
     * Lists all Categories
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
        $sid = $simupoll->getId();

        $tree = $this->categoryManager->getCategoryTreeNg($simupoll);

        return array(
            '_resource' => $simupoll,
            'sid'       => $sid,
            'tree'      => $tree
        );
    }

    /**
     * Json list of categories
     * @EXT\Route(
     *      "/categories/{id}",
     *      name="cpasimusante_simupoll_category_all",
     *      requirements={},
     *      options={"expose"=true}
     * )
     * @EXT\ParamConverter("simupoll", class="CPASimUSanteSimupollBundle:Simupoll", options={"id" = "id"})
     */
     public function getAllCategories(Simupoll $simupoll) {
         $tree = $this->categoryManager->getCategoryTreeNg($simupoll);
         return new JsonResponse($tree);
     }

    /**
     * Data for modal form for category add
     *
     * @EXT\Route(
     *     "/add/form/{cid}/{sid}",
     *     name="cpasimusante_simupoll_category_add_form",
     *     options = {"expose"=true}
     * )
     * @EXT\Template("CPASimUSanteSimupollBundle:Category:addCategory.html.twig")
     */
    public function categoryAddFormAction($cid, $sid)
    {
        $form = $this->get('form.factory')->create(new CategoryType());

        return array(
            'form'    => $form->createView(),
            'parent'  => $cid,
            'sid'     => $sid
        );
    }

    /**
     * Process category add
     *
     * @EXT\Route(
     *     "/add/{cid}/{sid}",
     *     name="cpasimusante_simupoll_category_add",
     *     options = {"expose"=true}
     * )
     * @EXT\Method("POST")
     * @EXT\Template("CPASimUSanteSimupollBundle:Category:addCategory.html.twig")
     */
    public function categoryAddAction(Request $request, $cid, $sid)
    {
        $form = $this->get('form.factory')->create(new CategoryType());

        $form->handleRequest($request);

        if ($form->isValid()) {
            $user = $this->tokenStorage->getToken()->getUser();

            $em = $this->getDoctrine()->getManager();
            $simupoll = $this->simupollManager->getSimupollById($sid);
            $newcat = $form->getData();
            //Add simupoll info
            $newcat->setSimupoll($simupoll);
            //add user info, for security
            $newcat->setUser($user);
            if ($cid != 0) {
                $category = $this->categoryManager->getCategoryByIdAndUser($cid, $user);
            }
            else {
                $category = null;
            }
            $newcat->setParent($category);
            $em->persist($newcat);
            $em->flush();
            return new JsonResponse('success', 200);
        }
        else {
            return array(
                'form'  => $form->createView(),
                'cid'   => $cid
            );
        }
    }

    /**
     * Process category delete
     *
     * @EXT\Route(
     *     "/delete/{cid}",
     *     name="cpasimusante_simupoll_category_delete_form",
     *     options = {"expose"=true}
     * )
     * @EXT\Template("CPASimUSanteSimupollBundle:Category:deleteCategory.html.twig")
     */
    public function categoryDeleteAction(Request $request, $cid)
    {
        if (!is_null($cid)) {
            $em = $this->getDoctrine()->getManager();
            $user = $this->tokenStorage->getToken()->getUser();
            $category = $this->categoryManager->getCategoryByIdAndUser($cid, $user);
            $em->remove($category);
            $em->flush();
            return new JsonResponse('success', 200);
        }
        else {
            return array();
        }
    }

    /**
     * Data for modal form for category modify
     *
     * @EXT\Route(
     *     "/modify/form/{cid}/{sid}",
     *     name="cpasimusante_simupoll_category_modify",
     *     options = {"expose"=true}
     * )
     * @EXT\Template("CPASimUSanteSimupollBundle:Category:modifyCategory.html.twig")
     */
    public function categoryModifyAction(Request $request, $cid, $sid)
    {
        $simupoll = $this->simupollManager->getSimupollById($sid);
        /*if (!$this->checkAccess('OPEN', $simupoll)) {
            throw new AccessDeniedException();
        }*/
        $user = $this->tokenStorage->getToken()->getUser();

        $category = $this->categoryManager->getCategoryByIdAndUser($cid, $user);

        $form = $this->get('form.factory')
            ->create(new CategoryType($simupoll, $category), $category, array('inside'=>true));
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em = $this->getDoctrine()->getManager();
            $em->persist($category);
            $em->flush();
            return new JsonResponse('success', 200);
        }

        return array(
            'form'    => $form->createView(),
            'parent'  => $cid,
            'sid'     => $sid
        );
    }

    /**
     * @EXT\Route("/category/add/{sid}", name="simupoll_add_category", options = {"expose"=true})
     * @EXT\Method("POST")
     *
     * @param Request $request
     * @param integer $sid
     *
     * @return JsonResponse
     */
    public function addCategoryAction(Request $request, $sid)
    {
        $user = $this->tokenStorage->getToken()->getUser();
        //$this->assertCanEdit($category->getResult());
        //retrive the data passed through the AJS CategoryService
        $cid = $request->request->get('cid');
        $categoryName = $request->request->get('name', false);
        //create response
        $response = new JsonResponse();
        //test if data is ok
        if ($categoryName !== false) {
            if ($categoryName == '') {
                $response->setData('Category is not valid');
                $response->setStatusCode(422);
            } else {
                $this->categoryManager->addCategory($sid, $cid, $user, $categoryName);
                //$response->setData($category->getId());
            }
        } else {
            $response->setData('Field "name" is missing');
            $response->setStatusCode(422);
        }

        return $response;
    }

    /**
     * @EXT\Route("/category/{id}", name="simupoll_edit_category")
     * @EXT\Method("PUT")
     *
     * @param Request $request
     * @param Category    $category
     *
     * @return JsonResponse
     */
    public function editCategoryAction(Request $request, Category $category)
    {
        $user = $this->tokenStorage->getToken()->getUser();
        //$this->assertCanEdit($category->getResult());
        $newParent = $request->request->get('parentcategory', false);
        $newName = $request->request->get('name', false);
        $response = new JsonResponse();

        if ($newName !== false) {
            if ($newName === '') {//TODO : test may be improved
                $response->setData('Category is not valid');
                $response->setStatusCode(422);
            } else {
                $this->manager->updateCategory($category, $user, $newName, $newParent);
                $response->setStatusCode(204);
            }
        } else {
            $response->setData('Field "name" is missing');
            $response->setStatusCode(422);
        }

        return $response;
    }

    /**
     * @EXT\Route("/category/{cid}", name="simupoll_delete_category", options = {"expose"=true})
     * @EXT\Method("DELETE")
     *
     * @param integer $cid
     *
     * @return JsonResponse
     */
    public function deleteCategoryAction($cid)
    {
        $user = $this->tokenStorage->getToken()->getUser();
        //$this->assertCanEdit($category->getResult());
        $this->categoryManager->deleteCategory($cid, $user);

        return new JsonResponse('', 204);
    }

    /**
     * @EXT\Route("/parent/{cid}/simupoll/{sid}", name="simupoll_parent_category", options = {"expose"=true})
     * @EXT\Method("GET")
     *
     * @param integer $cid
     *
     * @return JsonResponse
     */
    public function getParentCategoryAction($cid, $sid)
    {
        //$this->assertCanEdit($category->getResult());
        $user = $this->tokenStorage->getToken()->getUser();
        $simupoll = $this->simupollManager->getSimupollById($sid);
        $category = $this->categoryManager->getCategoryByIdAndUser($cid, $user);
        $data = $this->categoryManager->getParentCategories($simupoll, $category);
        
        return new JsonResponse($data, 200);
    }

    private function assertCanEdit(Category $category)
    {
        //"checker" = @DI\Inject("security.authorization_checker")
        //AuthorizationCheckerInterface $checker
        if (!$this->checker->isGranted('EDIT', $category)) {
            throw new AccessDeniedHttpException();
        }
    }
}
