<?php
namespace CPASimUSante\SimupollBundle\Controller;

use Claroline\CoreBundle\Library\Resource\ResourceCollection;
use CPASimUSante\SimupollBundle\Entity\Category;
use CPASimUSante\SimupollBundle\Entity\Simupoll;
use CPASimUSante\SimupollBundle\Entity\Paper;
use CPASimUSante\SimupollBundle\Entity\Answer;
use CPASimUSante\SimupollBundle\Entity\Question;
use CPASimUSante\SimupollBundle\Entity\Proposition;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as EXT;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

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
 *      "/",
 *      name    = "cpasimusante_simupoll_paper",
 * )
 */
class PaperController extends Controller
{
    /**
     * @EXT\Route(
     *      "/open/{id}/{page}/{all}",
     *      name="cpasimusante_simupoll_paper",
     *      defaults={ "page" = 1, "all" = 0 },
     *      requirements={},
     *      options={"expose"=true}
     * )
     * @EXT\Template("CPASimUSanteSimupollBundle:Paper:open.html.twig")
     */
     public function openAction(Request $request, Simupoll $simupoll)
     {
         $workspace = $simupoll->getResourceNode()->getWorkspace();
         $user = $this->container->get('security.token_storage')
             ->getToken()->getUser();
         $uid = $user->getId();

         $em = $this->getDoctrine()->getManager();
         $categories = $em->getRepository('CPASimUSanteSimupollBundle:Category')
             ->findBy(
                 array('simupoll' => $simupoll),
                 array('lft' => 'ASC')
             );
         $questions = $em->getRepository('CPASimUSanteSimupollBundle:Question')
             ->findBy(array('simupoll' => $simupoll));

         //http://stackoverflow.com/questions/28704738/symfony2-simple-file-upload-edit-without-entity
         $model = array(
             'questions' => $questions,
             'categories' => $categories
         );
         $builder = $this->createFormBuilder();
         $builder->setMethod('POST');
         $builder->add('questions', 'file');
         $builder->add('categories', 'file');
         $form = $builder->getForm();

         $session = $request->getSession();

         //$form = $this->createForm(AnswerType::class, $answer);

/*
         //Verify if it exists a not finished paper
         $paper = $this->getDoctrine()
             ->getManager()
             ->getRepository('UJMExoBundle:Paper')
             ->getPaper($uid, $simupoll);


         //if simupoll closed : redirect
         //if () {
         //    return $this->redirect($this->generateUrl('ujm_paper_list', array('exoID' => $id)));
         //}

         //if the paper doesn't exist or not finished
         if (count($paper) == 0) {
             $paper = new Paper();
             $paper->setSimupoll($simupoll);
             $paper->setUser($user);
             $paper->setStart(new \Datetime());

         } else {
             $paper = $paper[0];
         }
//$session->get('simupaper'),
         $session->save('simupaper', $paper->getId());
         $session->save('exerciseID', $simupoll->getId());
*/
         return array(
             'form'             => $form,
             'categories'       => $categories,
             'questions'        => $questions,
             'workspace'        => $workspace,
             '_resource'        => $simupoll
         );
     }

    /**
     * Lists all Paper entities.
     *
     * @EXT\Route(
     *      "/validate/{sid}/{page}/{all}",
     *      name="cpasimusante_simupoll_paper_save",
     *      defaults={ "page" = 1, "all" = 0 },
     *      requirements={},
     *      options={"expose"=true}
     * )
     * @access public
     *
     * @param integer $sid id of Simupoll
     * @param integer $page for the pagination, page destination
     * @param boolean $all for use or not use the pagination
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function paperSaveAction(Request $request, $sid, $page, $all)
    {
        $em = $this->getDoctrine()->getManager();
        $session = $request->getSession();
        //$paper = $em->getRepository('UJMExoBundle:Paper')->find($session->get('paper'));

        if ($request->isMethod('POST')) {
            $simupoll = $em->getRepository('CPASimUSanteSimupollBundle:Simupoll')->findOneById($sid);
            $user = $this->container->get('security.token_storage')->getToken()->getUser();
            $uid = $user->getId();

            //get NumPaper for the simupoll / user
            $dql = 'SELECT max(p.numPaper) FROM CPASimUSante\SimupollBundle\Entity\Paper p '
                . 'WHERE p.simupoll='.$sid.' AND p.user='.$uid;
            $query = $em->createQuery($dql);
            $maxNumPaper = $query->getSingleScalarResult();

            //Verify if it exists a not finished paper for the user
            $paper = $this->getDoctrine()
                ->getManager()
                ->getRepository('CPASimUSanteSimupollBundle:Paper')
                ->getPaper($uid, $sid);

            //list of answers
            $choices = $request->request->get('choice');

            //if there is no paper
          //  if (count($paper) == 0) {
                //Create paper
                $paper = new Paper();
                $paper->setUser($user);
                $paper->setStart(new \DateTime());
                $paper->setSimupoll($simupoll);
                $paper->setNumPaper((int) $maxNumPaper + 1);
                $em->persist($paper);
                $em->flush();

                //Save responses
                foreach($choices as $choice)
                {
                    $proposition = $em->getRepository('CPASimUSanteSimupollBundle:Proposition')
                        ->findOneById($choice[1]);
                    $answer = new Answer();
                    $answer->setPaper($paper);
                    $answer->setQuestion($proposition->getQuestion());
                    $answer->setAnswer($proposition->getId());
                    $answer->setMark($proposition->getMark());
                    $em->persist($answer);
                }
                $em->flush();

                //Set session
                $session->set('simupaper', $paper->getId());
          /*  } else {
                $paper = $paper[0];
            }*/

            $data = $choices;
            return new JsonResponse($data);
        }

       // $this->get('security.csrf.token_manager')->isTokenValid(new CsrfToken('', $token))
    }

    /**
     * Lists all Paper entities.
     *
     * @EXT\Route(
     *      "/papers/{id}/{page}/{all}",
     *      name="cpasimusante_simupoll_results_show",
     *      defaults={ "page" = 1, "all" = 0 },
     *      requirements={},
     *      options={"expose"=true}
     * )
     * @EXT\Template("CPASimUSanteSimupollBundle:Paper:list.html.twig")
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

        $workspace = $simupoll->getResourceNode()->getWorkspace();

        $exoAdmin = $exerciseSer->isExerciseAdmin($simupoll);

        $this->checkAccess($simupoll);

        if ($exoAdmin === true) {
            $paper = $this->getDoctrine()
                ->getManager()
                ->getRepository('UJMExoBundle:Paper')
                ->getExerciseAllPapers($simupoll->getId());
            $nbUserPaper = $exerciseSer->getNbPaper($user->getId(),
                $simupoll->getId());
        } else {
            $paper = $this->getDoctrine()
                ->getManager()
                ->getRepository('UJMExoBundle:Paper')
                ->getExerciseUserPapers($user->getId(), $simupoll->getId());
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

        if (($exerciseSer->controlDate($exoAdmin, $simupoll) === true)
            && ($exerciseSer->controlMaxAttemps($simupoll, $user, $exoAdmin) === true)
            && ( ($simupoll->getPublished() === true) || ($exoAdmin == 1) )
        ) {
            $retryButton = true;
        }

        if ($simupoll->getMaxAttempts() > 0) {
            if ($exoAdmin === false) {
                $nbAttemptAllowed = $simupoll->getMaxAttempts() - count($paper);
            }
        }

        $badgesInfoUser = $exerciseSer->badgesInfoUser(
            $user->getId(), $simupoll->getResourceNode()->getId(),
            $this->container->getParameter('locale'));

        $nbQuestions = $em->getRepository('CPASimUSanteSimupollBundle:SimupollGroupQuestion')
            ->getCountQuestion($simupoll->getId());

        return $this->render(
            'UJMExoBundle:Paper:index.html.twig',
            array(
                'workspace'        => $workspace,
                'papers'           => $papers,
                'isAdmin'          => $exoAdmin,
                'pager'            => $pagerfanta,
                'exoID'            => $simupoll->getId(),
                'display'          => $display,
                'retryButton'      => $retryButton,
                'nbAttemptAllowed' => $nbAttemptAllowed,
                'badgesInfoUser'   => $badgesInfoUser,
                'nbUserPaper'      => $nbUserPaper,
                'nbQuestions'      => $nbQuestions['nbq'],
                '_resource'        => $simupoll,
                'arrayMarkPapers'  => $arrayMarkPapers
            )
        );
    }
}