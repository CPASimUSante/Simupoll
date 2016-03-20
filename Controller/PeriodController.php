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
 *      name    = "cpasimusante_period",
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
     *     "/add/form/{idperiod}/{sid}",
     *     name="cpasimusante_simupoll_period_add_form",
     *     options = {"expose"=true}
     * )
     * @EXT\Template("CPASimUSanteSimupollBundle:Period:addPeriod.html.twig")
     */
    public function periodAddFormAction($idperiod, $sid)
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
     *     "/add/{idperiod}/{sid}",
     *     name="cpasimusante_simupoll_period_add",
     *     options = {"expose"=true}
     * )
     * @EXT\Method("POST")
     * @EXT\Template("CPASimUSanteSimupollBundle:Period:addPeriod.html.twig")
     */
    public function periodAddAction(Request $request, $idperiod, $sid)
    {
        $form = $this->get('form.factory')
            ->create(new PeriodType());
        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $simupoll = $this->simupollManager->getSimupollById($sid);
            $newperiod = $form->getData();
            //Add simupoll info
            $newperiod->setSimupoll($simupoll);

            $em->persist($newperiod);
            $em->flush();
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
}
