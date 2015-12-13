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
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * Class PaperController
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
 *      "/paper",
 *      name    = "cpasimusante_paper",
 * )
 */
class PaperController extends Controller
{
    /**
     * Lists all Paper entities.
     *
     * @EXT\Route(
     *      "/papers/{id}/{page}/{all}",
     *      name="cpasimusante_papers",
     *      defaults={ "page" = 1, "all" = 0 },
     *      requirements={},
     *      options={"expose"=true}
     * )
     * @EXT\Template("CPASimUSanteSimupollBundle:Papers:list.html.twig")
     *
     * @access public
     *
     * @param integer $simupoll id of Simupoll
     * @param integer $page for the pagination, page destination
     * @param boolean $all for use or not use the pagination
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function indexAction($simupoll, $page, $all)
    {
        $nbUserPaper = 0;
        $retryButton = false;
        $nbAttemptAllowed = -1;
        $exerciseSer = $this->container->get('ujm.exercise_services');

        $arrayMarkPapers = array();

        $user = $this->container->get('security.token_storage')->getToken()->getUser();

        $em = $this->getDoctrine()->getManager();
        $exercise = $em->getRepository('UJMExoBundle:Exercise')->find($exoID);
        $workspace = $exercise->getResourceNode()->getWorkspace();

        $exoAdmin = $exerciseSer->isExerciseAdmin($exercise);

        $this->checkAccess($exercise);

        if ($exoAdmin === true) {
            $paper = $this->getDoctrine()
                ->getManager()
                ->getRepository('UJMExoBundle:Paper')
                ->getExerciseAllPapers($exoID);
            $nbUserPaper = $exerciseSer->getNbPaper($user->getId(),
                $exercise->getId());
        } else {
            $paper = $this->getDoctrine()
                ->getManager()
                ->getRepository('UJMExoBundle:Paper')
                ->getExerciseUserPapers($user->getId(), $exoID);
            $nbUserPaper = count($paper);
        }

        // Pagination of the paper list
        if ($all == 1) {
            $max = count($paper);
        } else {
            $max = 10; // Max per page
        }

        $adapter = new ArrayAdapter($paper);
        $pagerfanta = new Pagerfanta($adapter);

        try {
            $papers = $pagerfanta
                ->setMaxPerPage($max)
                ->setCurrentPage($page)
                ->getCurrentPageResults();
        } catch (\Pagerfanta\Exception\NotValidCurrentPageException $e) {
            throw $this->createNotFoundException("Cette page n'existe pas.");
        }

        if (count($paper) > 0) {
            $display = $this->ctrlDisplayPaper($user, $paper[0]);
        } else {
            $display = 'all';
        }

        foreach ($paper as $p) {
            $arrayMarkPapers[$p->getId()] = $this->container->get('ujm.exercise_services')->getInfosPaper($p);
        }

        if (($exerciseSer->controlDate($exoAdmin, $exercise) === true)
            && ($exerciseSer->controlMaxAttemps($exercise, $user, $exoAdmin) === true)
            && ( ($exercise->getPublished() === true) || ($exoAdmin == 1) )
        ) {
            $retryButton = true;
        }

        if ($exercise->getMaxAttempts() > 0) {
            if ($exoAdmin === false) {
                $nbAttemptAllowed = $exercise->getMaxAttempts() - count($paper);
            }
        }

        $badgesInfoUser = $exerciseSer->badgesInfoUser(
            $user->getId(), $exercise->getResourceNode()->getId(),
            $this->container->getParameter('locale'));

        $nbQuestions = $em->getRepository('CPASimUSanteSimupollBundle:SimupollGroupQuestion')
            ->getCountQuestion($exoID);

        return $this->render(
            'UJMExoBundle:Paper:index.html.twig',
            array(
                'workspace'        => $workspace,
                'papers'           => $papers,
                'isAdmin'          => $exoAdmin,
                'pager'            => $pagerfanta,
                'exoID'            => $exoID,
                'display'          => $display,
                'retryButton'      => $retryButton,
                'nbAttemptAllowed' => $nbAttemptAllowed,
                'badgesInfoUser'   => $badgesInfoUser,
                'nbUserPaper'      => $nbUserPaper,
                'nbQuestions'      => $nbQuestions['nbq'],
                '_resource'        => $exercise,
                'arrayMarkPapers'  => $arrayMarkPapers
            )
        );
    }
}