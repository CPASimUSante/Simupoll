<?php

namespace CPASimUSante\SimupollBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Answer.
 *
 * @ORM\Table(name="cpasimusante__simupoll_answer")
 * @ORM\Entity(repositoryClass="CPASimUSante\SimupollBundle\Repository\AnswerRepository")
 */
class Answer
{
    /**
     * @var int
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var float
     *
     * @ORM\Column(name="mark", type="float")
     */
    private $mark;

    /**
     * @var string
     *
     * @ORM\Column(name="answer", type="text")
     */
    private $answer;

    /**
     * @ORM\ManyToOne(targetEntity="CPASimUSante\SimupollBundle\Entity\Paper")
     * @ORM\JoinColumn(name="paper_id", referencedColumnName="id",onDelete="CASCADE")
     */
    private $paper;

    /**
     * @ORM\ManyToOne(targetEntity="CPASimUSante\SimupollBundle\Entity\Question")
     * @ORM\JoinColumn(name="question_id", referencedColumnName="id",onDelete="CASCADE")
     */
    private $question;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set mark.
     *
     * @param float $mark
     *
     * @return Response
     */
    public function setMark($mark)
    {
        $this->mark = $mark;

        return $this;
    }

    /**
     * Get mark.
     *
     * @return float
     */
    public function getMark()
    {
        return $this->mark;
    }

    /**
     * Set response.
     *
     * @param string $response
     *
     * @return Response
     */
    public function setAnswer($answer)
    {
        $this->answer = $answer;

        return $this;
    }

    /**
     * Get answer.
     *
     * @return string
     */
    public function getAnswer()
    {
        return $this->answer;
    }

    public function setPaper(\CPASimUSante\SimupollBundle\Entity\Paper $paper)
    {
        $this->paper = $paper;
    }

    public function getPaper()
    {
        return $this->paper;
    }

    /**
     * Set question.
     *
     * @param string $question
     */
    public function setQuestion($question)
    {
        $this->question = $question;
    }

    /**
     * Get question.
     *
     * @return string
     */
    public function getQuestion()
    {
        return $this->question;
    }
}
