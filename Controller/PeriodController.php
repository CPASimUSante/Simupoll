<?php
namespace CPASimUSante\SimupollBundle\Controller;

use CPASimUSante\SimupollBundle\Entity\Period;
use CPASimUSante\SimupollBundle\Form\PeriodType;
use CPASimUSante\SimupollBundle\Entity\Simupoll;
use Doctrine\Common\Collections\ArrayCollection;
//use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as EXT;
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
        $em = $this->getDoctrine()->getManager();
        $sid = $simupoll->getId();

        //Custom query to display only tree from this resource
        $query = $em
            ->createQueryBuilder()
            ->select('p')
            ->from('CPASimUSante\SimupollBundle\Entity\Period', 'p')
            ->where('p.simupoll = ?1')
            ->setParameters(array(1 => $simupoll))
            ->getQuery();

        $periods = $query->getArrayResult();

        return array(
            '_resource' => $simupoll,
            'sid'       => $sid,
            'periods'     => $periods
        );
    }

    /**
     * Data for modal form for period add
     *
     * @EXT\Route(
     *     "/add/form/{idperiod}/{idsimupoll}",
     *     name="cpasimusante_simupoll_period_add_form",
     *     options = {"expose"=true}
     * )
     * @EXT\Template("CPASimUSanteSimupollBundle:Period:addPeriod.html.twig")
     */
    public function periodAddFormAction($idperiod, $idsimupoll)
    {
        $form = $this->get('form.factory')
            ->create(new PeriodType());
        return array(
            'form'          => $form->createView(),
            'idsimupoll'    => $idsimupoll
        );
    }

    /**
     * Process period add
     *
     * @EXT\Route(
     *     "/add/{idperiod}/{idsimupoll}",
     *     name="cpasimusante_simupoll_period_add",
     *     options = {"expose"=true}
     * )
     * @EXT\Method("POST")
     * @EXT\Template("CPASimUSanteSimupollBundle:Period:addPeriod.html.twig")
     */
    public function periodAddAction(Request $request, $idperiod, $idsimupoll)
    {
        $form = $this->get('form.factory')
            ->create(new PeriodType());
        $form->handleRequest($request);

        if ($form->isValid()) {

            $em = $this->getDoctrine()->getManager();
            $simupoll = $em->getRepository('CPASimUSanteSimupollBundle:Simupoll')
                ->findOneById($idsimupoll);
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
     *     "/delete/{idperiod}/{$idsimupoll}",
     *     name="cpasimusante_simupoll_period_delete_form",
     *     options = {"expose"=true}
     * )
     * @EXT\Template("CPASimUSanteSimupollBundle:Period:deletePeriod.html.twig")
     */
    public function periodDeleteAction(Request $request, $idperiod, $idsimupoll)
    {
        if (!is_null($idperiod)) {
            $em = $this->getDoctrine()->getManager();
            $period = $em->getRepository('CPASimUSanteSimupollBundle:Period')
                ->findOneBy(
                    array(
                        'id'        =>$idperiod,
                        'simupoll'  =>$idsimupoll
                    ));
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
     *     "/modify/form/{idperiod}/{idsimupoll}",
     *     name="cpasimusante_simupoll_period_modify",
     *     options = {"expose"=true}
     * )
     * @EXT\Template("CPASimUSanteSimupollBundle:Period:modifyPeriod.html.twig")
     */
    public function periodModifyAction(Request $request, $idperiod, $idsimupoll)
    {
        $em = $this->getDoctrine()->getManager();
        $period = $em->getRepository('CPASimUSanteSimupollBundle:Period')
            ->findOneBy(
                array(
                    'id'=>$idperiod,
                    'simupoll'=>$idsimupoll
                ));
        $form = $this->get('form.factory')
            ->create(new PeriodType(), $period);
        $form->handleRequest($request);

        if ($form->isValid()) {
            $em->persist($period);
            $em->flush();
            return new JsonResponse('success', 200);
        }

        return array(
            'form'          => $form->createView(),
            'parent'        => $idperiod,
            'idsimupoll'    => $idsimupoll
        );
    }
}
