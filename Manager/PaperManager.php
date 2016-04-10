<?php

namespace CPASimUSante\SimupollBundle\Manager;

use JMS\DiExtraBundle\Annotation as DI;
use Doctrine\ORM\EntityManager;
use Claroline\CoreBundle\Persistence\ObjectManager;

use Symfony\Component\HttpFoundation\Request;

use CPASimUSante\SimupollBundle\Entity\Simupoll;
use CPASimUSante\SimupollBundle\Entity\Answer;
use CPASimUSante\SimupollBundle\Entity\Paper;

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
     * get list of answers to be reused in form
     *
     * @return array $answers
     */
     public function getAnswers($arrPapersIds, $sid, $answers, $current_category, $next_category)
     {
         $answers = array();
         foreach ($arrPapersIds as $paperId) {
             $answers = $this->getAnswerDataForPaperInCategorylist($sid, $paperId, $answers, $current_category, $next_category);
         }
         return $answers;
     }

    /**
     * Find answers for categories
     *
     * @param $simupoll integer
     * @param $pid
     * @param $answers
     * @param $current_category
     * @param $next_category
     * @return array('questions', 'answers');
     */
    public function getAnswerDataForPaperInCategorylist($sid, $pid, $answers = array(), $current_category=-1, $next_category=-1)
    {
        $answersList = $this->om->getRepository('CPASimUSanteSimupollBundle:Answer')
            ->getAnswersForQuestions($sid, $pid, $current_category, $next_category);
        if ($answersList != null) {
            foreach($answersList as $a) {
                $answers[$a['qid'].'-'. $a['period']] = $a['answer'];
            }
        }
        return $answers;
    }

    /**
     *
     * @param $sid integer
     * @param $uid integer user id
     * @return mixed integer | null
     */
    public function getMaxNumPaper($sid, $uid)
    {
        $dql = 'SELECT max(p.numPaper) FROM CPASimUSante\SimupollBundle\Entity\Paper p '
            . 'WHERE p.simupoll='.$sid.' AND p.user='.$uid;
        $query = $this->om->createQuery($dql);
        return $query->getSingleScalarResult();
    }

    /**
     *
     * @param $sid integer
     * @param $uid integer user id
     * @return mixed integer | null
     */
    public function getPaper($sid, $uid)
    {
        return $this->om->getRepository('CPASimUSanteSimupollBundle:Paper')
            ->getPaper($uid, $sid);
    }

    /**
     * Save Paper
     * @return Paper $paper
     */
     public function savePaper($user, $simupoll, $period, $maxNumPaper)
     {
         $paper = new Paper();
         $paper->setUser($user);
         $paper->setStart(new \DateTime());
         $paper->setSimupoll($simupoll);
         $paper->setPeriod($period);
         $paper->setNumPaper($maxNumPaper);
         $this->om->persist($paper);
         $this->om->flush();
         return $paper;
     }

     /**
      * Save Answers
      *
      * @param $choices array list of answers (checked radioboxes values)
      * @param $arrPaper array array of saved paper
      * @return void
      */
     public function saveAnswers($choices, $arrPaper)
     {
         //Then, save the answers
         foreach($choices as $key => $propo) {
             //retrieve data from $key : question_id - period_id and $propo : proposition_id
             $atmp  = explode('-', $key);
             $quest_id = $atmp[0];
             $per_id = $atmp[1];
             //get proposition
             $proposition = $this->om->getRepository('CPASimUSanteSimupollBundle:Proposition')
                ->findOneById($propo);
             // get paper
             $thepaper = $arrPaper[$per_id];
echo '$per_id'.$per_id;echo '</pre>';var_dump(is_object($arrPaper[$per_id]));echo '</pre><br>';
             if (is_object($arrPaper[$per_id])) {
                //save answer
                $answer = new Answer();
                $answer->setPaper($thepaper);
                $answer->setQuestion($proposition->getQuestion());
                $answer->setAnswer($proposition->getId().';');
                $answer->setMark($proposition->getMark());
                $this->om->persist($answer);
            }
        }
        $this->om->flush();
    }
}
