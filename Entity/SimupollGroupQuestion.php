<?php

namespace CPASimUSante\SimupollBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use CPASimUSante\SimupollBundle\Entity\Simupoll;
use CPASimUSante\SimupollBundle\Entity\Question;
use CPASimUSante\SimupollBundle\Entity\QuestionGroup;

/**
 * CPASimUSante\SimupollBundle\Entity\SimupollGroupQuestion
 *
 * @ORM\Entity(repositoryClass="CPASimUSante\SimupollBundle\Repository\SimupollGroupQuestionRepository")
 * @ORM\Table(name="cpasimusante__simupoll_group_question")
 */
class SimupollGroupQuestion
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="CPASimUSante\SimupollBundle\Entity\Simupoll")
     */
    private $simupoll;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="CPASimUSante\SimupollBundle\Entity\QuestionGroup")
     */
    private $questiongroup;

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="CPASimUSante\SimupollBundle\Entity\Question")
     */
    private $question;

    /**
     * @var integer $ordre
     *
     * @ORM\Column(name="ordre", type="integer")
     */
    private $ordre;


    public function __construct(Simupoll $simupoll, Question $question, QuestionGroup $questiongroup)
    {
        $this->simupoll = $simupoll;
        $this->question = $question;
        $this->questiongroup = $questiongroup;
    }

    public function setSimupoll(Simupoll $simupoll)
    {
        $this->simupoll = $simupoll;
    }

    public function getSimupoll()
    {
        return $this->simupoll;
    }

    public function setQuestion(Question $question)
    {
        $this->question = $question;
    }

    public function getQuestion()
    {
        return $this->question;
    }

    public function setQuestionGroup(QuestionGroup $questiongroup)
    {
        $this->questiongroup = $questiongroup;
    }

    public function getQuestionGroup()
    {
        return $this->questiongroup;
    }

    /**
     * Set ordre
     *
     * @param integer $ordre
     */
    public function setOrdre($ordre)
    {
        $this->ordre = $ordre;
    }

    /**
     * Get ordre
     *
     * @return integer
     */
    public function getOrdre()
    {
        return $this->ordre;
    }
}
