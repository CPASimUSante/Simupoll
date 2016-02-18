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
     * Prepare the data to display the paper
     *
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

         $session = $request->getSession();
         $em = $this->getDoctrine()->getManager();

         $current = -1;
         $next = -1;
         //1 - is the simupoll opened now ? = between start-stop in Period
         //TODO : create manager, also used to show (or not) the answer button
         $start = '';
         $stop = '';
         $openedPeriod = $em->getRepository('CPASimUSanteSimupollBundle:Period')
             ->getOpenedPeriodForSimupoll($simupoll->getId());
         if ($openedPeriod != null) {
             $start = $openedPeriod[0]->getStart();
             $stop = $openedPeriod[0]->getStop();
         }

         //retrieve the data to drive how the questions are displayed
         $displayOrganization = $em->getRepository('CPASimUSanteSimupollBundle:Organization')
             ->findOneBySimupoll($simupoll);
         $choice = 0;
         $choiceData = '';
         if (isset($displayOrganization)) {
             $choice = $displayOrganization->getChoice();
             $choiceData = $displayOrganization->getChoiceData();
         }

         //get all categories (could be refined, but would need more request)
         $categories = $em->getRepository('CPASimUSanteSimupollBundle:Category')
             ->findBy(
                 array('simupoll' => $simupoll),
                 array('lft' => 'ASC')
             );

         //we have simupaper session
         if ($session->get('simupaper') != null) {
             echo 'A';
             //display X questions per page
             if ($choice == 1) {                        //OK
                 $limit = (int)$choiceData;
                 $offset = 0; //TODO
                 $questions = $em->getRepository('CPASimUSanteSimupollBundle:Question')
                     ->getQuestionsWithAnswers($simupoll->getId(), $session->get('simupaper'), $limit, $offset);
                 //display group of questions
             } elseif ($choice == 2) {echo 'B';
                 $data = $this->setQuestionDataForPaperInCategorylist($request, $simupoll, $session->get('simupaper'), $choiceData);
                 $questions = $data['question'];
                 $next = $data['next'];
                 $current = $data['current'];
             }
             $pid = $session->get('simupaper');
         //we don't have simupaper session
         } else {echo 'C';
             $paper = $em->getRepository('CPASimUSanteSimupollBundle:Paper')
                 ->getCurrentPaper($uid, $simupoll->getId(), $start, $stop);
             // no simupaper session but a paper already exists
             if ($paper != null) {echo 'D';
                 $pid = $paper->getId();
                 //display X questions per page
                 if ($choice == 1) {      echo 'E';              //OK
                     $limit = (int)$choiceData;
                     $offset = 0; //TODO
                     $questions = $em->getRepository('CPASimUSanteSimupollBundle:Question')
                         ->getQuestionsWithAnswers($simupoll->getId(), $pid, $limit, $offset);
                 //display question from range of categories
                 } elseif ($choice == 2) {echo 'F';
                     $data = $this->setQuestionDataForPaperInCategorylist($request, $simupoll, $pid, $choiceData);
                     $questions = $data['question'];
                     $next = $data['next'];
                     $current = $data['current'];
                 }
             // no simupaper session and no paper already exists
             } else {echo 'G';
                 $pid = 0;
                 //display X questions per page
                 if ($choice == 1) {    echo 'H';                //OK
                     $limit = (int)$choiceData;
                     $offset = 0; //TODO
                     $questions = $em->getRepository('CPASimUSanteSimupollBundle:Question')
                         ->findBy(array('simupoll' => $simupoll), null, $limit, $offset);
                 //display question from range of categories
                 } elseif ($choice == 2) {echo 'I';
                     //list of selected categories
                     $data = $this->setQuestionDataForPaperInCategorylist($request, $simupoll, $pid, $choiceData);
                     $questions = $data['question'];
                     $next = $data['next'];
                     $current = $data['current'];
                 //all questions
                 } else {     echo 'J';                          //OK
                     $questions = $em->getRepository('CPASimUSanteSimupollBundle:Question')
                         ->findBy(array('simupoll' => $simupoll));
                 }
             }
         }
 //        foreach ($questions as $question) {echo $question[0]->getName().'<br>';}die();
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
*/
         return array(
             'pid'              => $pid,
             'categories'       => $categories,
             'questions'        => $questions,
             'next'             => $next,
             'current'          => $current,
             'workspace'        => $workspace,
             '_resource'        => $simupoll
         );
     }

    private function setQuestionDataForPaperInCategorylist(Request $request, Simupoll $simupoll, $pid, $choiceData)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();

        $current = -1;
        $next = -1;
        $categorybounds = explode(',', $choiceData);

        //we have catpaper session
        if ($session->get('catpaper') != null) {echo 'x1';
            if (count($categorybounds) > 1) {echo 'x2';
                $keys = array_keys($categorybounds);
                //we didn't reach the end
                $prev = $session->get('catpaper')['next'];
                if (isset($keys[array_search($prev, $categorybounds)+1])) {echo 'x3';
                    $next = $categorybounds[$keys[array_search($prev, $categorybounds)+1]];
                    $current = $prev;
                //last category
                } else {echo 'x4';
                    $next = -1;
                    $current = $session->get('catpaper')['next'];  //finished
                }
                $questions = $em->getRepository('CPASimUSanteSimupollBundle:Question')
                    ->getQuestionsWithAnswersInCategories($simupoll->getId(), $pid, $prev, $next);
            } else {echo 'x5';
                $questions = $em->getRepository('CPASimUSanteSimupollBundle:Question')
                    ->findBy(array('simupoll' => $simupoll));
            }
            //we don't have catpaper session : we don't know where we are
        } else {     echo 'x6';                      //OK
            //no bounds = Only root : get all questions !
            if (count($categorybounds) == 1) {echo 'x7';
                $questions = $em->getRepository('CPASimUSanteSimupollBundle:Question')
                    ->findBy(array('simupoll' => $simupoll));
                //bounds exist : get questions
            } else {echo 'x8';
                $questions = $em->getRepository('CPASimUSanteSimupollBundle:Question')
                    ->getQuestionsWithAnswersInCategories($simupoll->getId(), $pid, 0, $categorybounds[1]);
                $next = $categorybounds[1];
                $current = 0;
            }
        }
        echo 'xxx';
        //foreach ($questions as $question) {echo $question[0]->getTitle().'<br>';}echo 'xxx';
        //echo $questions;
        //die();
        //echo '<pre>';var_dump($questions);echo '</pre>';die();
        return array('question'=> $questions, 'next' => $next, 'current' => $current);
    }

    /**
     * json request, save the paper data
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
//echo '<pre>';var_dump($session->get('simupaper'));echo '</pre>';die();
        if ($request->isMethod('POST')) {
            $simupoll = $em->getRepository('CPASimUSanteSimupollBundle:Simupoll')
                ->findOneById($sid);
            $user = $this->container->get('security.token_storage')
                ->getToken()->getUser();
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

            $next = $request->request->get('next');
            $prev = $request->request->get('prev');

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
                    $answer->setAnswer($proposition->getId().';');
                    $answer->setMark($proposition->getMark());
                    $em->persist($answer);
                }
                $em->flush();

                //Set session
                $session->set('simupaper', $paper->getId());

//            $session->set('catpaper', array('next' =>$next, 'prev'=>$prev));
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