<?php

namespace CPASimUSante\SimupollBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use CPASimUSante\SimupollBundle\Entity\Question;

/**
 * Proposition
 *
 * @ORM\Table(name="cpasimusante__simupoll_proposition")
 * @ORM\Entity(repositoryClass="CPASimUSante\SimupollBundle\Repository\PropositionRepository")
 */
class Proposition
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var Question
     *
     * @ORM\ManyToOne(targetEntity="CPASimUSante\SimupollBundle\Entity\Question", inversedBy="propositions")
     * @ORM\JoinColumn(name="question_id", referencedColumnName="id")
     */
    protected $question;

    /**
     * @var string $choice
     *
     * @ORM\Column(name="choice", type="string", length=255)
     */
    private $choice;

    /**
     * Get id
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Get question
     *
     * @return \CPASimUSante\SimupollBundle\Entity\Question
     */
    public function getQuestion()
    {
        return $this->question;
    }

    /**
     * Set question
     *
     * @param \CPASimUSante\SimupollBundle\Entity\Question $question
     *
     * @return Proposition
     */
    public function setQuestion(Question $question = null)
    {
        $this->question = $question;

        return $this;
    }

    /**
     * Get choice
     */
    public function getChoice()
    {
        return $this->choice;
    }

    /**
     * Set itemselector
     * @param string $choice
     * @return Proposition
     */
    public function setChoice($choice)
    {
        $this->choice = $choice;

        return $this;
    }
}