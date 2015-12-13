<?php
namespace CPASimUSante\SimupollBundle\Controller;
use Claroline\CoreBundle\Library\Resource\ResourceCollection;
use CPASimUSante\SimupollBundle\Entity\Simupoll;
use CPASimUSante\SimupollBundle\Form\SimupollType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as EXT;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
/**
 * Class SimupollController
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
 *      "/",
 *      name    = "cpasimusante_simupoll",
 *      service = "cpasimusante_simupoll.controller.simupoll"
 * )
 */
class SimupollController extends Controller
{
    /**
     *
     * @EXT\Route("/edit/{id}", name="cpasimusante_editsimupoll", requirements={"id" = "\d+"}, options={"expose"=true})
     * @EXT\ParamConverter("simupoll", class="CPASimUSanteSimupollBundle:Simupoll", options={"id" = "id"})
     * @EXT\Template("CPASimUSanteSimupollBundle:Simupoll:edit.html.twig")
     * @param Request $request
     * @param Simupoll $simupoll
     * @return array
     */
    public function editAction(Request $request, Simupoll $simupoll)
    {
        //can user access ?
        $this->checkAccess($simupoll);
        //can user edit ?
        $simupollAdmin = $this->container->get('cpasimusante_simupoll.services.simupoll')->isSimupollAdmin($simupoll);
        if ($simupollAdmin === true)
        {
            $form = $this->get('form.factory')
                ->create(new SimupollType(), $simupoll);
            $form->handleRequest($request);
            if ($form->isValid()) {
                $em = $this->getDoctrine()->getManager();
                $em->persist($simupoll);
                $em->flush();
            }
            return array(
                '_resource' => $simupoll,
                'form' => $form->createView(),
            );
        }
        //If not admin, open
        else
        {
            return $this->redirect($this->generateUrl('cpasimusante_simupoll_open', array('simupollId' => $simupoll->getId())));
        }
    }
    /**
     *
     * @EXT\Route("/open/{id}", name="cpasimusante_opensimupoll", requirements={"id" = "\d+"}, options={"expose"=true})
     * @EXT\ParamConverter("simupoll", class="CPASimUSanteSimupollBundle:Simupoll", options={"id" = "id"})
     * @EXT\Template("CPASimUSanteSimupollBundle:Simupoll:open.html.twig")
     * @param Simupoll $simupoll
     * @return array
     */
    public function openAction($simupoll)
    {
        return array(
            '_resource' => $simupoll,
        );
    }
    /**
     * To check the right to open or not
     *
     * @access private
     *
     * @param \CPASimUSante\SimupollBundle\Entity\Simupoll $simupoll
     *
     * @return exception
     */
    private function checkAccess($simupoll)
    {
        $collection = new ResourceCollection(array($simupoll->getResourceNode()));
        if (!$this->get('security.authorization_checker')->isGranted('OPEN', $collection)) {
            throw new AccessDeniedException($collection->getErrorsForDisplay());
        }
    }
}