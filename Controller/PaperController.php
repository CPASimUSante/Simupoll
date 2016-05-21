<?php

namespace CPASimUSante\SimupollBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration as EXT;
use JMS\DiExtraBundle\Annotation as DI;
use CPASimUSante\SimupollBundle\Manager\PaperManager;
use CPASimUSante\SimupollBundle\Manager\PeriodManager;
use CPASimUSante\SimupollBundle\Entity\Category;
use CPASimUSante\SimupollBundle\Entity\Simupoll;
use CPASimUSante\SimupollBundle\Entity\Paper;
use CPASimUSante\SimupollBundle\Entity\Answer;
use CPASimUSante\SimupollBundle\Entity\Question;
use CPASimUSante\SimupollBundle\Entity\Proposition;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class PaperController.
 *
 * @category   Controller
 *
 * @author     CPASimUSante <contact@simusante.com>
 * @copyright  2015 CPASimUSante
 * @license    http://www.opensource.org/licenses/mit-license.php MIT License
 *
 * @version    0.1
 *
 * @link       http://simusante.com
 *
 * @EXT\Route(
 *      "/",
 *      name    = "cpasimusante_simupoll_paper",
 * )
 */
class PaperController extends Controller
{
    private $paperManager;
    private $periodManager;
    private $session;
    private $translator;

    /**
     * @DI\InjectParams({
     *     "paperManager"   = @DI\Inject("cpasimusante.simupoll.paper_manager"),
     *     "periodManager"   = @DI\Inject("cpasimusante.simupoll.period_manager"),
     *     "session"        = @DI\Inject("session"),
     *     "translator"     = @DI\Inject("translator")
     * })
     *
     * @param paperManager   PaperManager
     * @param periodManager   PeriodManager
     */
    public function __construct(
        PaperManager $paperManager,
        PeriodManager $periodManager,
        SessionInterface $session,
        TranslatorInterface $translator
    ) {
        $this->paperManager = $paperManager;
        $this->periodManager = $periodManager;
        $this->session = $session;
        $this->translator = $translator;
    }

     /**
      * Prepare the data to display the paper.
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
         $sessionFlashBag = $this->session->getFlashBag();
         $workspace = $simupoll->getResourceNode()->getWorkspace();
         $session = $request->getSession();
         $em = $this->getDoctrine()->getManager();
         $user = $this->container->get('security.token_storage')
             ->getToken()->getUser();
         if (is_string($user)) {
         } else {
         }
         $uid = $user->getId();
         $sid = $simupoll->getId();
         $arrPapersIds = array();
         $arrPeriodsIds = array();
         $categorybounds = array();
         $answers = null;
         $maxNumPaper = 0;
         $current_page = 1;
         $total_page = 1;
         $previous_category = -1;
         $current_category = 0;
         $next_category = -1;

         //get all periods for the Simupoll
        $periods = $this->periodManager->getPeriods($sid);
/*
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
*/
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
                 //did user choose several categories
                 if (isset($categorybounds[1])) {
                     $next_category = $categorybounds[1];
                 }
             }
         } else {
             //no organization set : get out
             $msg = $this->translator->trans('organization_not_set', array(), 'resource');
             $sessionFlashBag->add('error', $msg);

             return $this->redirect($this->generateUrl('cpasimusante_simupoll_open', array('id' => $sid)));
         }

//echo '<pre>$periods[id]=';var_dump($periods['id']);echo '</pre>';echo ' user_id='.$uid;echo ' simupoll_id='.$sid;

        //3 - Get paper ids (list of id)
        //periodIds >= paperIds = sessionIds
        //paper already created, and simupaper session exists
        //$session->get('simupaper') = paperid1-periodid1;paperid2-periodid2...
        if ($session->get('simupaper') != null) {
            $tmps = explode(';', $session->get('simupaper'));
            foreach ($tmps as $tmp) {
                $t = explode('-', $tmp);
                $arrPapersIds[$t[1]] = $t[0];
                $arrPeriodsIds[] = $t[1];
            }
            $maxNumPaper = count($tmps);
//echo '<br>session exists<br>';echo $session->get('simupaper');echo '<br><pre>$arrPapersIds =<br>';var_dump(array_keys($arrPapersIds));echo '</pre>';
            // if (count($arrPapersIds) != count($session_papersIds))
        } else {
            $papers = $em->getRepository('CPASimUSanteSimupollBundle:Paper')
                ->findByUserAndSimupoll($uid, $sid);
            if ($papers != array()) {
                foreach ($papers as $paper) {
                    $periodId = $paper->getPeriod()->getId();
                    $arrPapersIds[$periodId] = $paper->getId();
                    $arrPeriodsIds[] = $periodId;
                }
            }
//echo '<br>session does not exist<br>';echo '<pre>$arrPapersIds =<br>';var_dump($arrPapersIds);echo '</pre>';
            $maxNumPaper = count($papers);
        }

/*
         //3 - Get paper id
         //paper already created, and simupaper session exists
         if ($session->get('simupaper') != null) {
             $paperId = $session->get('simupaper');
             $paper = $em->getRepository('CPASimUSanteSimupollBundle:Paper')
                 ->findOneBy(array('user'=>$uid, 'id' => $paperId, 'period'=>$period_id));
                 //->getPaper($uid, $paperId, $period_id);
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
                 $paperId = $paper->getId();
             }
             echo ' c='.$paperId.'<br>';
         }
         echo '<pre>';var_dump(gettype($paper));echo '</pre>';
*/
         //4 - the save part
         if ($request->isMethod('POST')) {
             $next_category = $request->request->get('next');
             $current_category = $request->request->get('current');

//echo '<br>IN POST 1- current_category '.$current_category.' next_category '.$next_category.'<br><b>current_page='.$current_page;echo '</b><br>';
             //4 - 1 save paper
             $arrPaper = array();
             $tmp = array();
             //for each period
             foreach ($periods['id'] as $key => $per_id) {
                 //echo $per_id.' -key='.$key.'<br>';
                 //if paper has not been saved
                 if (!array_key_exists($per_id, $arrPapersIds)) {
                     //echo 'new paper, period='.$per_id.'<br>';
                     $maxNumPaper = $maxNumPaper + 1;
                     //save the paper
                     $paper = $this->paperManager
                        ->savePaper($user, $simupoll, $periods['entity'][$key], $maxNumPaper);
                     /*
                     $paper = new Paper();
                     $paper->setUser($user);
                     $paper->setStart(new \DateTime());
                     $paper->setSimupoll($simupoll);
                     $paper->setPeriod($periods['entity'][$key]);
                     $paper->setNumPaper($maxNumPaper);
                     $em->persist($paper);
                     $em->flush();
                     */
                     $arrPaper[$per_id] = $paper;
                     //session data
                     $tmp[] = $paper->getId().'-'.$per_id;
                 } else {
                     //echo 'paper exists, period='.$per_id.'<br>';
                     $arrPaper[$per_id] = $em->getRepository('CPASimUSanteSimupollBundle:Paper')
                         ->findOneById($arrPapersIds[$per_id]);
                    //session data
                     $tmp[] = $arrPapersIds[$per_id].'-'.$per_id;
                 }
             }
//echo '<pre>$arrPaper =<br>';var_dump(array_keys($arrPaper));echo '</pre>';
//die();
             //Set session
             $session->set('simupaper', implode(';', $tmp));

             //list of answers
             $choices = $request->request->get('choice');

             //4 - 2 Save user Answers
             if ($choices != null) {
                 //get questions for the selected categories
                 $questionList = $em->getRepository('CPASimUSanteSimupollBundle:Question')
                     ->getQuestionsWithinCategories($sid, $current_category, $next_category);

                 //First, remove the already saved answers
                 $qids = array();
                 if ($questionList != null) {
                     //get question id array
                     foreach ($questionList as $ql) {
                         $qids[] = $ql['id'];
                     }
                     if ($qids != array()) {
                         //remove old answers, for each paper = corresponding to each period
                         foreach ($arrPapersIds as $paperId) {
                             $em->getRepository('CPASimUSanteSimupollBundle:Answer')
                                ->deleteOldAnswersInCategories($paperId, $qids);
                         }
//echo 'pid'.$pid;echo '<pre>';var_dump(implode(',',$al));echo '</pre>';
                     }
                 }

                //Then, save the answers
                //$this->paperManager->saveAnswers($choices, $arrPaper);

                 foreach ($choices as $key => $propo) {
                     //retrieve data from $key : question_id - period_id and $propo : proposition_id
                     $atmp = explode('-', $key);
                     $quest_id = $atmp[0];
                     $per_id = $atmp[1];
                     //get proposition
                     $proposition = $em->getRepository('CPASimUSanteSimupollBundle:Proposition')
                         ->findOneById($propo);
                     // get paper
                     $thepaper = $arrPaper[$per_id];
//echo '$per_id'.$per_id;echo '</pre>';var_dump(is_object($arrPaper[$per_id]));echo '</pre>';
                    //save answer
                    if (is_object($arrPaper[$per_id])) {
                        $answer = new Answer();
                        $answer->setPaper($thepaper);
                        $answer->setQuestion($proposition->getQuestion());
                        $answer->setAnswer($proposition->getId().';');
                        $answer->setMark($proposition->getMark());
                        $em->persist($answer);
                    }
                 }
                 $em->flush();
             }
         }

//echo '2- current_category '.$current_category.' next_category '.$next_category.'<br><b>current_page='.$current_page;echo '</b><br>';

         //5 - get general data for display questions in categories
         //get new bounds
         $tmp_current = $current_category;
         $tmp_next = $next_category;
         $tmp_answers = array();

         if ($choice == 2) {
             if ($next_category == -1) {
                 $current_page = $total_page;
             }
             if ($current_category == -1) {
                 $current_page = 1;
             }
             if ($request->isMethod('POST')) {
                 $direction = $request->request->get('direction');
//echo 'next ';echo '<pre>next=';var_dump($request->request->get('next'));echo '</pre>';echo '<br>';echo 'direction ';var_dump($direction);echo '<br>';
                //click on Next button
                 if ($direction != 'prev') {
                     //get current pos. of "next"
                     $k = array_search($next_category, $categorybounds);
                     if ($k !== false) {
                         //echo 'k='.$k.'<br>';echo '3next - current_category '.$current_category.' next_category '.$next_category.'<br><b>current_page='.$current_page;echo '</b><br>';
                         $current_category = $next_category;
                         $current_page = $k + 1;
                         //not the last position
                         if (isset($categorybounds[$k + 1])) {
                             $next_category = $categorybounds[$k + 1];
//echo 'isset<br>';
                             //last pos
                         } else {
                             $next_category = -1;
                         }
                         // $next_category already -1
                     } else {
                         $next_category = -1;
//echo 'next, k=false<br>';
                     }
                //click on Previous button
                 } else {
                     $k = array_search($current_category, $categorybounds);
                     if ($k !== false) {
                         $next_category = $current_category;
                         $current_page = $k;
//echo 'k='.$k.'<br>';echo '3prev- current_category '.$current_category.' next_category '.$next_category.'<br><b>current_page='.$current_page;echo '</b><br>';
                         //not the first position
                         if (isset($categorybounds[$k - 1])) {
                             $current_category = $categorybounds[$k - 1];
//echo 'isset<br>';
                             //last pos
                         } else {
                             $current_category = -1;
                         }
                     } else {
                         $current_category = -1;
//echo 'prev, k=false<br>';
                     }
                 }
             }
//echo '4- current_category '.$current_category.' next_category '.$next_category.'<br><b>current_page='.$current_page;echo '</b><br>';

             //manage bounds
             //find categories between the bounds
             $categories = $em->getRepository('CPASimUSanteSimupollBundle:Category')
                 ->getCategoriesBetweenLft($sid, $current_category, $next_category);
//echo '<pre>cats=';foreach($categories as $c){var_dump($c->getId());}echo '</pre>'; //OK

             //find questions and answers for these categories
             $questions = $em->getRepository('CPASimUSanteSimupollBundle:Question')
                ->getQuestionsWithCategories($sid, $current_category, $next_category);
             $answers = $this->paperManager
                ->getAnswers($arrPapersIds, $sid, $answers, $current_category, $next_category);
/*
            $answers = array();
            foreach ($arrPapersIds as $paperId) {
                $answers = $this->paperManager
                    ->getAnswerDataForPaperInCategorylist($sid, $paperId, $answers, $current_category, $next_category);
            }
            */
            //$answers = array_merge($tmp_answers);
//echo '<pre>$answers partial =';var_dump($answers);echo '</pre>'; //OK
         //case : all questions shown
         } else {
             //get all categories
             $categories = $em->getRepository('CPASimUSanteSimupollBundle:Category')
                 ->findBy(
                     array('simupoll' => $simupoll),
                     array('lft' => 'ASC')
                 );
/*
             foreach ($arrPapersIds as $paperId) {
                 $tmp_answers = $this->paperManager
                     ->getAnswerDataForPaperInCategorylist($sid, $paperId, $tmp_answers, $current_category, $next_category);
             }
*/
             $tmp_answers = $this->paperManager
                 ->getAnswers($arrPapersIds, $sid, $tmp_answers, $current_category, $next_category);
             $answers = array_merge($tmp_answers);
//echo '$answers all =<br><pre>';var_dump($answers);echo '</pre><br>';
             //get all questions and propositions
             $questions = $em->getRepository('CPASimUSanteSimupollBundle:Question')
                 ->getQuestionsWithCategories($sid);
         }

// echo '5- current_category '.$current_category.' next_category '.$next_category.'<br><b>current_page='.$current_page;echo '</b><br>';
// echo '<pre>$arrPapersIds';var_dump($arrPapersIds);echo '</pre>';
// echo '<pre>$periods';var_dump($periods['current']);echo '</pre>';

         return array(
             'choice' => $choice,
             'pids' => $arrPapersIds,
             'page' => $current_page,
             'total' => $total_page,
             'categories' => $categories,
             'questions' => $questions,
             'answers' => $answers,
             'next' => $next_category,
             'current' => $current_category,
             'periods' => $periods,    //array of all periods (id, title) for the simupoll
             'workspace' => $workspace,
             '_resource' => $simupoll,
         );
     }

    /**
     * json request, save the paper data.
     *
     * @EXT\Route(
     *      "/validate/{sid}/{page}/{all}",
     *      name="cpasimusante_simupoll_paper_save",
     *      defaults={ "page" = 1, "all" = 0 },
     *      requirements={},
     *      options={"expose"=true}
     * )
     *
     * @param int  $sid  id of Simupoll
     * @param int  $page for the pagination, page destination
     * @param bool $all  for use or not use the pagination
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
            $maxNumPaper = $this->paperManager->getMaxNumPaper($sid, $uid);

            //Verify if it exists a not finished paper for the user
            //$paper = $this->paperManager->gePaper($sid, $uid);

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
                foreach ($choices as $choice) {
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
}
