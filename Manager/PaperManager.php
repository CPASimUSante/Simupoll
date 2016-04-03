<?php

namespace CPASimUSante\SimupollBundle\Manager;

use JMS\DiExtraBundle\Annotation as DI;
use Doctrine\ORM\EntityManager;
use Claroline\CoreBundle\Persistence\ObjectManager;

use Symfony\Component\HttpFoundation\Request;

use CPASimUSante\SimupollBundle\Entity\Simupoll;

/**
 * Helper functions for papers
 *
 * @DI\Service("cpasimusante.simupoll.paper_manager")
 */
class PaperManager
{
    private $om;

    /**
     * @DI\InjectParams({
     *     "om" = @DI\Inject("claroline.persistence.object_manager")
     * })
     *
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * Find questions and answers for categories
     *
     * @param $simupoll integer
     * @param $pid
     * @param $current_category
     * @param $next_category
     * @return array('questions', 'answers');
     */
    public function getAnswerDataForPaperInCategorylist($sid, $pid, $answers = array(), $current_category=-1, $next_category=-1)
    {
        $answersList = $this->om->getRepository('CPASimUSanteSimupollBundle:Answer')
            ->getAnswersForQuestions($sid, $pid, $current_category, $next_category);

        //$answers = array();
        if ($answersList != null) {
            foreach($answersList as $a) {
                $answers[$a['qid'].'-'. $a['period']] = $a['answer'];
            }
        }
//echo '<pre>$answers=';var_dump($answers);echo '</pre>'; //OK
        return $answers;
    }

    /**
     * Find questions and answers for categories
     * @param Request $request
     * @param Simupoll $simupoll
     * @param $pid
     * @param $categorybounds
     * @param $current_category
     * @param $next_category
     * @return array('questions', 'answers');
     */
    public function setQuestionDataForPaperInCategorylist(Request $request, Simupoll $simupoll, $pid, $categorybounds, $current_category, $next_category)
    {
        //$session = $request->getSession();


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

        $answersList = $this->om->getRepository('CPASimUSanteSimupollBundle:Answer')
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
}
