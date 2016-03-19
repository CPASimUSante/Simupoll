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
//use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration as EXT;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Csrf\CsrfToken;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use Pagerfanta\Adapter\ArrayAdapter;
use Pagerfanta\Pagerfanta;

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
         $sid = $simupoll->getId();
         $pid = 0;
         $categorybounds = array();
         $answers = null;

         $session = $request->getSession();
         $em = $this->getDoctrine()->getManager();

         $current_page = 1;
         $total_page = 1;
         $previous_category = -1;
         $current_category = 0;
         $next_category = -1;

         //1 - is the simupoll opened now ? = between start-stop in Period
         //TODO : should not be needed, if the answer button is not shown
         $period_start = '';
         $period_stop = '';
         $period_id = 0;
         $period = null;
         $openedPeriod = $em->getRepository('CPASimUSanteSimupollBundle:Period')
             ->getOpenedPeriodForSimupoll($sid);
         if ($openedPeriod != null) {
             $period_id = $openedPeriod[0]->getId();
             $period_start = $openedPeriod[0]->getStart();
             $period_stop = $openedPeriod[0]->getStop();
             $period = $openedPeriod[0];
         } else {
             //get out
             return $this->redirect($this->generateUrl('cpasimusante_simupoll_open', array('id' => $sid)));
         }

         //2 - retrieve the data to drive how the questions are displayed
         //all questions, from categories ... ?
         $displayOrganization = $em->getRepository('CPASimUSanteSimupollBundle:Organization')
             ->findOneBySimupoll($simupoll);
         $choice = 0;
         $choiceData = '';
         if (isset($displayOrganization)) {
             $choice = $displayOrganization->getChoice();
             $choiceData = $displayOrganization->getChoiceData();
             //if several categories
             if ($choice == 2) {
                 $categorybounds = explode(',', $choiceData);
                 $total_page = count($categorybounds);
                 //default values
                 $current_category = $categorybounds[0];
                 //(did user choose several categories)
                 if (isset($categorybounds[1])) {
                     $next_category = $categorybounds[1];
                 }
             }
         } else {
             //get out
             return $this->redirect($this->generateUrl('cpasimusante_simupoll_open', array('id' => $sid)));
         }
echo ' period_id='.$period_id;
echo ' user_id='.$uid;
echo ' simupoll_id='.$sid;
         //3 - get paper id
         //paper already created, and simupaper session exists
         if ($session->get('simupaper') != null) {
             $pid = $session->get('simupaper');
             $paper = $em->getRepository('CPASimUSanteSimupollBundle:Paper')
                 ->findOneBy(array('user'=>$uid, 'id' => $pid, 'period'=>$period_id));
                 //->getPaper($uid, $pid, $period_id);
             echo ' a ';
             //we don't have simupaper session
         } else {
             $paper = $em->getRepository('CPASimUSanteSimupollBundle:Paper')
                 ->findOneBy(array('user'=>$uid, 'period'=>$period_id));//
                 //->getCurrentPaperInPeriod($uid, $sid, $period_id);
                 //->getCurrentPaper($uid, $sid, $period_start, $period_stop);
             // no simupaper session but a paper already exists (new login)
             echo ' b ';
             if ($paper != array()) {
                 $pid = $paper->getId();
             }
             echo ' c='.$pid.'<br>';
         }
         echo '<pre>';var_dump(gettype($paper));echo '</pre>';
         //4 - the save part
         if ($request->isMethod('POST')) {
/*
             //get NumPaper for the simupoll / user
             $dql = 'SELECT max(p.numPaper) FROM CPASimUSante\SimupollBundle\Entity\Paper p '
                 . 'WHERE p.simupoll='.$sid.' AND p.user='.$uid;
             $query = $em->createQuery($dql);
             $maxNumPaper = $query->getSingleScalarResult();
*/
             //list of answers
             $choices = $request->request->get('choice');

             $next_category = $request->request->get('next');
             $current_category = $request->request->get('current');
echo 'IN POST 1- current_category '.$current_category.' next_category '.$next_category.'<br><b>current_page='.$current_page;echo '</b><br>';
             //save paper
             if ($pid == 0) {
                 //Create paper
                 $paper = new Paper();
                 $paper->setUser($user);
                 $paper->setStart(new \DateTime());
                 $paper->setSimupoll($simupoll);
                 $paper->setPeriod($period);
                 $paper->setNumPaper(1);
                 $em->persist($paper);
                 $em->flush();
                 //Set session
                 $session->set('simupaper', $paper->getId());
             } else {
                 $maxNumPaper = $paper->getNumPaper() + 1;
                 $paper->setNumPaper((int)$maxNumPaper);
                 $em->persist($paper);
                 $em->flush();
             }

             //Save user answers
             if ($choices != null) {
                 //get question list
                 $questionList = $em->getRepository('CPASimUSanteSimupollBundle:Question')
                     ->getQuestionsWithinCategories($sid, $current_category, $next_category);
                 $al = array();
                 if ($questionList != null){
                     foreach($questionList as $ql){$al[] = $ql['id'];}
                     if ($al != array()) {
                         //remove old answers
                         $em->getRepository('CPASimUSanteSimupollBundle:Answer')
                             ->deleteOldAnswersInCategories($pid, $al);

echo 'pid'.$pid;echo '<pre>';var_dump(implode(',',$al));echo '</pre>';
                     }
                 }

                 foreach($choices as $c) {
                     $proposition = $em->getRepository('CPASimUSanteSimupollBundle:Proposition')
                         ->findOneById($c);
                     $answer = new Answer();
                     $answer->setPaper($paper);
                     $answer->setQuestion($proposition->getQuestion());
                     $answer->setAnswer($proposition->getId().';');
                     $answer->setMark($proposition->getMark());
                     $em->persist($answer);
                 }
                 $em->flush();
             }
         }
echo '2- current_category '.$current_category.' next_category '.$next_category.'<br><b>current_page='.$current_page;echo '</b><br>';
         //5 - get general data for display
         //questions in categories
         //get new bounds
         $tmp_current = $current_category;
         $tmp_next = $next_category;
         if ($choice == 2) {
             if ($next_category == -1){$current_page = $total_page;}
             if ($current_category == -1){$current_page = 1;}
             if ($request->isMethod('POST')) {
                 $direction = $request->request->get('direction');
echo 'next ';var_dump($request->request->get('next'));echo '<br>';
echo 'direction ';var_dump($direction);echo '<br>';
                 if ($direction != 'prev') {
                     //get current pos. of "next"
                     $k = array_search($next_category, $categorybounds);
                     if ($k !== false) {
echo 'k='.$k.'<br>';
echo '3next - current_category '.$current_category.' next_category '.$next_category.'<br><b>current_page='.$current_page;echo '</b><br>';
                         $current_category = $next_category;
                         $current_page = $k + 1;
                         //not the last position
                         if (isset($categorybounds[$k + 1])) {
                             echo 'isset<br>';
                             $next_category = $categorybounds[$k + 1];
                             //last pos
                         } else {
                             $next_category = -1;
                         }
                         // $next_category already -1
                     } else {
                         echo 'next, k=false<br>';
                         $next_category = -1;
                     }
                 } else {
                     $k = array_search($current_category, $categorybounds);
                     if ($k !== false) {
echo 'k='.$k.'<br>';
echo '3prev- current_category '.$current_category.' next_category '.$next_category.'<br><b>current_page='.$current_page;echo '</b><br>';
                         $next_category = $current_category;
                         $current_page = $k;
                         //not the first position
                         if (isset($categorybounds[$k-1])) {
                             echo 'isset<br>';
                             $current_category = $categorybounds[$k-1];
                             //last pos
                         } else {
                             $current_category = -1;
                         }
                     } else {
                         echo 'prev, k=false<br>';
                         $current_category = -1;
                     }
                 }

             }
echo '4- current_category '.$current_category.' next_category '.$next_category.'<br><b>current_page='.$current_page;echo '</b><br>';

             //manage bounds
             $categories = $em->getRepository('CPASimUSanteSimupollBundle:Category')
                 ->getCategoriesBetweenLft($simupoll->getId(), $current_category, $next_category);
foreach($categories as $c){var_dump($c->getId());} //OK
             $data = $this->setQuestionDataForPaperInCategorylist($request, $simupoll, $pid, $categorybounds, $current_category, $next_category);
             $questions = $data['questions'];
             $answers = $data['answers'];
         //all questions
         } else {
             //get all categories
             $categories = $em->getRepository('CPASimUSanteSimupollBundle:Category')
                 ->findBy(
                     array('simupoll' => $simupoll),
                     array('lft' => 'ASC')
                 );
             $answersList = $em->getRepository('CPASimUSanteSimupollBundle:Answer')
                 ->getAnswersForQuestions($simupoll->getId(), $pid);

             $answers = array();
             if ($answersList != null) {
                 foreach($answersList as $a) {
                     $answers[$a['qid']] = array('id' => $a['id'], 'answer' => $a['answer']);
                 }
             }
             $limit = (int)$choiceData;
             $offset = 0; //TODO
             //get all questions and propositions
             $questions = $em->getRepository('CPASimUSanteSimupollBundle:Question')
                 ->getQuestionsWithCategories($sid);
                 //->getQuestionsWithAnswers($sid, $pid, $limit, $offset);
                 //only from categories
         }

echo '5- current_category '.$current_category.' next_category '.$next_category.' paper '.$pid.'<br><b>current_page='.$current_page;echo '</b><br>';

         return array(
             'choice'           => $choice,
             'pid'              => $pid,
             'page'             => $current_page,
             'total'            => $total_page,
             'categories'       => $categories,
             'questions'        => $questions,
             'answers'          => $answers,
             'next'             => $next_category,
             'current'          => $current_category,
             'workspace'        => $workspace,
             '_resource'        => $simupoll,
             //'pager'            => $pagerfanta
         );
     }

    private function setQuestionDataForPaperInCategorylist(Request $request, Simupoll $simupoll, $pid, $categorybounds, $current_category, $next_category)
    {
        $session = $request->getSession();
        $em = $this->getDoctrine()->getManager();

        $questions = $em->getRepository('CPASimUSanteSimupollBundle:Question')
            ->getQuestionsWithCategories($simupoll->getId(), $current_category, $next_category);
/*
        $answers = $em->getRepository('CPASimUSanteSimupollBundle:Question')
            ->getQuestionsWithAnswersInCategories($simupoll->getId(), $pid, $current_category, $next_category);
        $answ = array();
        if ($answers != null) {
            foreach ($answers as $answer) {
                //var_dump($answer[0]->getId());
                var_dump($answer->getAnswer());
                die();
                $answ[$answer[0]->getId()] = $answer->getId();
            }
        }
        */

        $answersList = $em->getRepository('CPASimUSanteSimupollBundle:Answer')
            ->getAnswersForQuestions($simupoll->getId(), $pid, $current_category, $next_category);

        $answers = array();
        if ($answersList != null) {
            foreach($answersList as $a) {
                $answers[$a['qid']] = array('id' => $a['id'], 'answer' => $a['answer']);
            }
        }

        /*
           $answers = $em->getRepository('CPASimUSanteSimupollBundle:Question')
            ->getQuestionsInCategories($simupoll->getId(), $pid, $current_category, $next_category);
        */


        /*
        //we have catpaper session
        if ($session->get('catpaper') != null) {
echo 'x1';
            if (count($categorybounds) > 1) {
echo 'x2';
                $keys = array_keys($categorybounds);
                //we didn't reach the end
                $prev = $session->get('catpaper')['next'];
                if (isset($keys[array_search($prev, $categorybounds)+1])) {
echo 'x3';
                    $next_category = $categorybounds[$keys[array_search($prev, $categorybounds)+1]];
                    $current_category = $prev;
                //last category
                } else {
echo 'x4';
                    $next_category = -1;
                    $current_category = $session->get('catpaper')['next'];  //finished
                }
                $questions = $em->getRepository('CPASimUSanteSimupollBundle:Question')
                    ->getQuestionsWithAnswersInCategories($simupoll->getId(), $pid, $prev, $next_category);
            } else {
echo 'x5';
                $questions = $em->getRepository('CPASimUSanteSimupollBundle:Question')
                    ->findBy(array('simupoll' => $simupoll));
            }
            //we don't have catpaper session : we don't know where we are
        } else {
echo 'x6';                      //OK
            //no bounds = Only root : get all questions !
            if (count($categorybounds) == 1) {
echo 'x7';
                $questions = $em->getRepository('CPASimUSanteSimupollBundle:Question')
                    ->findBy(array('simupoll' => $simupoll));
                //bounds exist : get questions
            } else {
echo 'x8';
                $questions = $em->getRepository('CPASimUSanteSimupollBundle:Question')
                    ->getQuestionsWithAnswersInCategories($simupoll->getId(), $pid, 0, $categorybounds[1]);
                $next_category = $categorybounds[1];
                $current_category = 0;
            }
        }
        */


        //foreach ($questions as $question) {echo $question[0]->getTitle().'<br>';}echo 'xxx';
        //echo $questions;
        //die();
        //echo '<pre>';var_dump($questions);echo '</pre>';die();
        return array('questions'=> $questions, 'answers' => $answers);
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

            $next_category = $request->request->get('next');
            $current_category = $request->request->get('current');

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

            $data = $choices;
            return new JsonResponse($data);
        }
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
