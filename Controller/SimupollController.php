<?php
namespace CPASimUSante\SimupollBundle\Controller;

use Claroline\CoreBundle\Library\Resource\ResourceCollection;
use CPASimUSante\SimupollBundle\Entity\Simupoll;
use CPASimUSante\SimupollBundle\Form\CategoryType;
use CPASimUSante\SimupollBundle\Form\SimupollType;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as EXT;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
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
     * Manage the form to create the simupoll
     *
     * @EXT\Route("/edit/{id}", name="cpasimusante_simupoll_edit", requirements={"id" = "\d+"}, options={"expose"=true})
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
        $simupollAdmin = $this->container
            ->get('cpasimusante_simupoll.services.simupoll')
            ->isSimupollAdmin($simupoll);

        if ($simupollAdmin === true)
        {
            $em = $this->getDoctrine()->getManager();

            // Create an array of the current Question objects in the database
            $originalQuestions = array();
            foreach ($simupoll->getQuestions() as $question) {
                $originalQuestions[] = $question;
            }

            //pass the simupoll id to filter the categories
            $form = $this->get('form.factory')
                ->create(new SimupollType($simupoll->getId()), $simupoll);

            $form->handleRequest($request);
            if ($form->isValid()) {

                // remove the relationship between the question and the Simupoll
                foreach ($originalQuestions as $question) {
                    if (false === $simupoll->getQuestions()->contains($question)) {
                        // remove the Simupoll from the question
                        //$question->getSimupoll()->removeElement($simupoll);

                        // in a a ManyToOne relationship, remove the relationship
                       // $question->setSimupoll(null);
                       // $em->persist($question);

                        // to delete the question entirely, you can also do that
                        $em->remove($question);
                    }
                }
//echo '<pre>';var_dump();echo '</pre>';die();
                $em->persist($simupoll);
                $em->flush();
                $this->get('session')->getFlashBag()->add('info', 'Simupoll mis à jour');
            }
            return array(
                '_resource'     => $simupoll,
                'form'          => $form->createView(),
            );
        }
        //If not admin, open
        else
        {
            return $this->redirect($this->generateUrl('cpasimusante_simupoll_open', array('simupollId' => $simupoll->getId())));
        }
    }

    /**
     * When opening the Simupoll resource
     *
     * @EXT\Route("/open/{id}", name="cpasimusante_simupoll_open", requirements={"id" = "\d+"}, options={"expose"=true})
     * @EXT\ParamConverter("simupoll", class="CPASimUSanteSimupollBundle:Simupoll", options={"id" = "id"})
     * @EXT\Template("CPASimUSanteSimupollBundle:Simupoll:open.html.twig")
     * @param Simupoll $simupoll
     * @return array
     */
    public function openAction($simupoll)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->container->get('security.token_storage')
            ->getToken()->getUser();

        //can user access ?
        $this->checkAccess($simupoll);

        //can user edit ?
        $simupollAdmin = $this->container
            ->get('cpasimusante_simupoll.services.simupoll')
            ->isSimupollAdmin($simupoll);

        //can user manage exercise
        $allowToCompose = 0;

        if (is_object($user) && ($simupollAdmin === true) )
        {
            $allowToCompose = 1;
        }

        return array(
            '_resource'         => $simupoll,
            'allowToCompose'    => $allowToCompose
        );
    }

    /**
     * Organizing the Simupoll resource
     *
     * @EXT\Route("/organize/{id}", name="cpasimusante_simupoll_organize", requirements={"id" = "\d+"}, options={"expose"=true})
     * @EXT\ParamConverter("simupoll", class="CPASimUSanteSimupollBundle:Simupoll", options={"id" = "id"})
     * @EXT\Template("CPASimUSanteSimupollBundle:Simupoll:organize.html.twig")
     * @param Simupoll $simupoll
     * @return array
     */
    public function organizeAction($simupoll)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->container->get('security.token_storage')
            ->getToken()->getUser();

        //can user access ?
        $this->checkAccess($simupoll);

        //can user edit ?
        $simupollAdmin = $this->container
            ->get('cpasimusante_simupoll.services.simupoll')
            ->isSimupollAdmin($simupoll);

        //can user manage exercise
        $allowToCompose = 0;
        if (is_object($user) && ($simupollAdmin === true) )
        {
            $allowToCompose = 1;
            $repo = $em->getRepository('CPASimUSanteSimupollBundle:Category');
            //retrieve max level of category
            $maxCategoryLevel = $repo->getMaxLevel($simupoll);

            //display tree of categories for group
            $query = $em->createQueryBuilder()
                ->select('node')
                ->from('CPASimUSante\SimupollBundle\Entity\Category', 'node')
                ->orderBy('node.root, node.lft', 'ASC')
                ->where('node.simupoll = ?1')
                ->setParameters(array(1 => $simupoll))
                ->getQuery();
            $repoCat = $em->getRepository('CPASimUSanteSimupollBundle:Question');

            $options = array(
                'decorate' => true,
                'rootOpen' => '',
                'rootClose' => '',
                'childOpen' => '<tr>',
                'childClose' => '</tr>',
                'nodeDecorator' => function($node) use ($repoCat) {
                    $qcount = $repoCat->getQuestionCount($node['id']);
                    $input = ' <input type="checkbox" data-id="'.$node['id'].'" name="categorygroup[]">';
                    return '<td>'.$input.'</td><td>'.$qcount.'</td><td>'.str_repeat("=",($node['lvl'])*2).' '.$node['name'].'</td>';
                }
            );
            $tree = $repo->buildTree($query->getArrayResult(), $options);

            return array(
                'tree'              => $tree,
                'maxCategoryLevel'  => $maxCategoryLevel['maxlevel'],
                'allowToCompose'    => $allowToCompose,
                '_resource'         => $simupoll,
            );
        } else {
            return $this->redirect($this->generateUrl('cpasimusante_simupoll_open', array('simupollId' => $simupoll->getId())));
        }
    }

    /**
     * Display the statistics choices
     *
     * @EXT\Route("/result/{id}", name="cpasimusante_simupoll_results", requirements={"id" = "\d+"}, options={"expose"=true})
     * @EXT\ParamConverter("simupoll", class="CPASimUSanteSimupollBundle:Simupoll", options={"id" = "id"})
     * @EXT\Template("CPASimUSanteSimupollBundle:Simupoll:results.html.twig")
     * @param Simupoll $simupoll
     * @return array
     */
    public function resultsAction($simupoll)
    {
        $em = $this->getDoctrine()->getManager();

        $user = $this->container->get('security.token_storage')
            ->getToken()->getUser();

        //can user access ?
        $this->checkAccess($simupoll);

        //can user edit ?
        $simupollAdmin = $this->container
            ->get('cpasimusante_simupoll.services.simupoll')
            ->isSimupollAdmin($simupoll);

        //can user manage exercise
        $allowToCompose = 0;
        if (is_object($user) && ($simupollAdmin === true) )
        {
            $allowToCompose = 1;

            return array(
                'allowToCompose'    => $allowToCompose,
                '_resource'         => $simupoll,
            );
        } else {
            return $this->redirect($this->generateUrl('cpasimusante_simupoll_open', array('simupollId' => $simupoll->getId())));
        }
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


    /**
     *
     */
    public function prepareResultsAndStatsForSimupoll()
    {
        $em = $this->getDoctrine()->getManager();

    }

    /**
     * Display the statistics for the simupoll
     *
     * @EXT\Route("/showgeneralstats/{id}", name="cpasimusante_simupoll_stats_allhtml", requirements={"id" = "\d+"}, options={"expose"=true})
     * @EXT\ParamConverter("simupoll", class="CPASimUSanteSimupollBundle:Simupoll", options={"id" = "id"})
     * @EXT\Template("CPASimUSanteSimupollBundle:Simupoll:showstats.html.twig")
     * @param Simupoll $simupoll
     * @return array
     */
    public function getResultAllhtmlAction(Simupoll $simupoll)
    {
        $html = '';

        return array(
            '_resource'         => $simupoll,
        );
    }

    /**
     * Export the statistics for the simupoll
     *
     * @EXT\Route("/exportstats/{id}", name="cpasimusante_simupoll_stats_csv", requirements={"id" = "\d+"}, options={"expose"=true})
     * @EXT\ParamConverter("simupoll", class="CPASimUSanteSimupollBundle:Simupoll", options={"id" = "id"})
     * @param Simupoll $simupoll
     * @return array
     */
    public function getResultCsvAction($simupoll)
    {
        $date = new \DateTime();
        $now = $date->format('Y-m-d-His');

        $content = '';

        return new Response($content, 200, array(
            'Content-Type' => 'application/force-download',
            'Content-Disposition' => 'attachment; filename="exportall-'.$now.'.csv"'
        ));
    }

    /**
     * Prepare the statistics in json for radar display
     *
     * @EXT\Route("/jsonstats/{id}", name="cpasimusante_simupoll_stats_json", requirements={"id" = "\d+"}, options={"expose"=true})
     * @EXT\ParamConverter("simupoll", class="CPASimUSanteSimupollBundle:Simupoll", options={"id" = "id"})
     * @param Simupoll $simupoll
     * @return array
     */
    public function getResultJsonAction($simupoll)
    {

    }
}