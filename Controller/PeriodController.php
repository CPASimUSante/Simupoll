<?php
namespace CPASimUSante\SimupollBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration as EXT;
use JMS\DiExtraBundle\Annotation as DI;

use CPASimUSante\SimupollBundle\Manager\SimupollManager;
use CPASimUSante\SimupollBundle\Manager\PeriodManager;
use CPASimUSante\SimupollBundle\Entity\Period;
use CPASimUSante\SimupollBundle\Form\PeriodType;
use CPASimUSante\SimupollBundle\Entity\Simupoll;
use Doctrine\Common\Collections\ArrayCollection;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class PeriodController
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
 *      name    = "cpasimusante_simupoll_period",
 * )
 */
class PeriodController extends Controller
{
    private $simupollManager;
    private $periodManager;

    /**
     * @DI\InjectParams({
     *     "simupollManager"  = @DI\Inject("cpasimusante.simupoll.simupoll_manager"),
     *     "periodManager"  = @DI\Inject("cpasimusante.simupoll.period_manager"),
     * })
     *
     * @param SimupollManager   simupollManager
     * @param PeriodManager     periodManager
     */
    public function __construct(
        SimupollManager $simupollManager,
        PeriodManager $periodManager
    )
    {
      $this->simupollManager = $simupollManager;
      $this->periodManager = $periodManager;
    }

    /**
     * Lists all Period
     *
     * @EXT\Route(
     *      "/periods/{id}",
     *      name="cpasimusante_simupoll_period_manage",
     *      requirements={},
     *      options={"expose"=true}
     * )
     * @EXT\ParamConverter("simupoll", class="CPASimUSanteSimupollBundle:Simupoll", options={"id" = "id"})
     * @EXT\Template("CPASimUSanteSimupollBundle:Period:list.html.twig")
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

        $periods = $this->periodManager->getPeriodBySimupoll($simupoll);

        return array(
            '_resource' => $simupoll,
            'sid'       => $sid,
            'periods'   => $periods
        );
    }

    /**
     * Data for modal form for period add
     *
     * @EXT\Route(
     *     "/add/form/{sid}",
     *     name="cpasimusante_simupoll_period_add_form",
     *     options = {"expose"=true}
     * )
     * @EXT\Template("CPASimUSanteSimupollBundle:Period:addPeriod.html.twig")
     */
    public function periodAddFormAction($sid)
    {
        $form = $this->get('form.factory')
            ->create(new PeriodType());
        return array(
            'form'   => $form->createView(),
            'sid'    => $sid
        );
    }

    /**
     * Process period add
     *
     * @EXT\Route(
     *     "/add/submit/{sid}",
     *     name="cpasimusante_simupoll_period_submit",
     *     options = {"expose"=true}
     * )
     * @EXT\Method("POST")
     * @EXT\Template("CPASimUSanteSimupollBundle:Period:addPeriod.html.twig")
     */
    public function periodAddAction(Request $request, $sid)
    {
        $form = $this->get('form.factory')
            ->create(new PeriodType());
        $form->handleRequest($request);
        $idperiod = 0;
        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $simupoll = $this->simupollManager->getSimupollById($sid);
            $newperiod = $form->getData();
//echo '<pre>';var_dump($newperiod);echo '</pre>';
            //Add simupoll info
            $newperiod->setSimupoll($simupoll);

            $em->persist($newperiod);
            $em->flush();
            $idperiod = $newperiod->getId();

            return new JsonResponse('success', 200);
        }
        else
        {
            return array(
                'form'        => $form->createView(),
                'idperiod'    => $idperiod
            );
        }
    }

    /**
     * Process period delete
     *
     * @EXT\Route(
     *     "/delete/{idperiod}/{sid}",
     *     name="cpasimusante_simupoll_period_delete_form",
     *     options = {"expose"=true}
     * )
     * @EXT\Template("CPASimUSanteSimupollBundle:Period:deletePeriod.html.twig")
     */
    public function periodDeleteAction(Request $request, $idperiod, $sid)
    {
        if (!is_null($idperiod)) {
            $em = $this->getDoctrine()->getManager();
            $period = $this->periodManager->getPeriodBySimupollAndId($idperiod, $sid);
            $em->remove($period);
            $em->flush();
            return new JsonResponse('success', 200);
        }
        else
        {
            return array();
        }
    }

    /**
     * Data for modal form for period modify
     *
     * @EXT\Route(
     *     "/modify/form/{idperiod}/{sid}",
     *     name="cpasimusante_simupoll_period_modify",
     *     options = {"expose"=true}
     * )
     * @EXT\Template("CPASimUSanteSimupollBundle:Period:modifyPeriod.html.twig")
     */
    public function periodModifyAction(Request $request, $idperiod, $sid)
    {
        $em = $this->getDoctrine()->getManager();
        $period = $this->periodManager->getPeriodBySimupollAndId($idperiod, $sid);

        $form = $this->get('form.factory')
            ->create(new PeriodType(), $period);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($period);
            $em->flush();
            return new JsonResponse('success', 200);
        }

        return array(
            'form'    => $form->createView(),
            'parent'  => $idperiod,
            'sid'     => $sid
        );
    }


    /**
     * Calls from Angular
     */

     /**
      * @EXT\Route("/period/delete/{pid}/{sid}", name="simupoll_delete_period", options = {"expose"=true})
      * @EXT\Method("DELETE")
      *
      * @param integer $pid
      * @param integer $sid
      *
      * @return JsonResponse
      */
     public function deletePeriod($pid, $sid)
     {
         //$this->assertCanEdit($category->getResult());
         $this->periodManager->deletePeriod($pid, $sid);

         return new JsonResponse('', 204);
     }

     /**
      * @EXT\Route("/period/add/{sid}", name="simupoll_add_period", options = {"expose"=true})
      * @EXT\Method("POST")
      *
      * @param Request $request
      * @param integer $sid
      *
      * @return JsonResponse
      */
     public function addPeriodAction(Request $request, $sid)
     {
         //$this->assertCanEdit($category->getResult());
         //retrive the data passed through the AJS CategoryService
         $periodTitle = $request->request->get('title', false);
         $periodStart = $request->request->get('start', false);
         $periodStop = $request->request->get('stop', false);
         //create response
         $response = new JsonResponse();
         //test if data is ok
         if (!($periodTitle === false || $periodStart === false || $periodStop === false)) {
             if ($periodTitle == '') {
                 $response->setData('Period title is not valid');
                 $response->setStatusCode(422);
             } else {
                 if ($periodStart == '') {
                     $response->setData('Period start is not valid');
                     $response->setStatusCode(422);
                 } else {
                     if ($periodStop == '') {
                         $response->setData('Period stop is not valid');
                         $response->setStatusCode(422);
                     } else {
                         $this->periodManager->addPeriod($sid, $periodTitle, $periodStart, $periodStop);
                         //$response->setData($category->getId());
                     }
                 }
             }
         } else {
             $response->setData('All fields must be filled');
             $response->setStatusCode(422);
         }

         return $response;
     }

     /**
      * @EXT\Route("/edit/{pid}/", name="simupoll_edit_period", options = {"expose"=true})
      * @EXT\ParamConverter("period", class="CPASimUSanteSimupollBundle:Period", options={"mapping": {"pid" = "id"}})
      * @EXT\Method("PUT")
      *
      * @param Request $request
      * @param Period    $period
      *
      * @return JsonResponse
      */
     public function editPeriodAction(Request $request, Period $period)
     {
         //$this->assertCanEdit($category->getResult());
         $periodTitle = $request->request->get('title', false);
         $periodStart = $request->request->get('start', false);
         $periodStop = $request->request->get('stop', false);
         $response = new JsonResponse();

         if (!($periodTitle === false || $periodStart === false || $periodStop === false)) {
             if ($periodTitle == '') {
                 $response->setData('Period title is not valid');
                 $response->setStatusCode(422);
             } else {
                 if ($periodStart == '') {
                     $response->setData('Period start is not valid');
                     $response->setStatusCode(422);
                 } else {
                     if ($periodStop == '') {
                         $response->setData('Period stop is not valid');
                         $response->setStatusCode(422);
                     } else {
                         $this->periodManager->updatePeriod($period, $periodTitle, $periodStart, $periodStop);
                     }
                 }
             }
         } else {
             $response->setData('All fields must be filled');
             $response->setStatusCode(422);
         }

         return $response;
     }
}
