<?php

/*
 * This file is part of the Claroline Connect package.
 *
 * (c) Claroline Consortium <consortium@claroline.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace CPASimUSante\SimupollBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use CPASimUSante\SimupollBundle\Entity\Simupoll;
use CPASimUSante\SimupollBundle\Entity\Question;

/**
 * @ORM\Table(name="cpasimusante__questiongroup")
 * @ORM\Entity(repositoryClass="CPASimUSante\SimupollBundle\Repository\QuestionGroupRepository")
 */
class QuestionGroup
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string $title
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var datetime $dateStart
     *
     * @ORM\Column(name="date_start", type="datetime")
     */
    private $dateStart;

    /**
     * @var datetime $dateEnd
     *
     * @ORM\Column(name="date_end", type="datetime", nullable=true)
     */
    private $dateEnd;

    /**
     * @var Simupoll
     *
     * @ORM\ManyToOne(targetEntity="CPASimUSante\SimupollBundle\Entity\Simupoll", inversedBy="questiongroups")
     * @ORM\JoinColumn(name="simupoll_id", referencedColumnName="id")
     */
    protected $simupoll;

    /**
     * @var Questions[]
     *
     * @ORM\OneToMany(targetEntity="CPASimUSante\SimupollBundle\Entity\Question", mappedBy="questiongroup", cascade={"all"})
     */
    protected $questions;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->questions = new ArrayCollection();
    }

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
     * Set title
     *
     * @param string $title
     * @return QuestionGroup
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string 
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set dateStart
     *
     * @param \DateTime $dateStart
     * @return QuestionGroup
     */
    public function setDateStart($dateStart)
    {
        $this->dateStart = $dateStart;

        return $this;
    }

    /**
     * Get dateStart
     *
     * @return \DateTime 
     */
    public function getDateStart()
    {
        return $this->dateStart;
    }

    /**
     * Set dateEnd
     *
     * @param \DateTime $dateEnd
     * @return QuestionGroup
     */
    public function setDateEnd($dateEnd)
    {
        $this->dateEnd = $dateEnd;

        return $this;
    }

    /**
     * Get dateEnd
     *
     * @return \DateTime 
     */
    public function getDateEnd()
    {
        return $this->dateEnd;
    }

    /**
     * Set simupoll
     *
     * @param \CPASimUSante\SimupollBundle\Entity\Simupoll $simupoll
     * @return QuestionGroup
     */
    public function setSimupoll(Simupoll $simupoll = null)
    {
        $this->simupoll = $simupoll;

        return $this;
    }

    /**
     * Get simupoll
     *
     * @return \CPASimUSante\SimupollBundle\Entity\Simupoll 
     */
    public function getSimupoll()
    {
        return $this->simupoll;
    }

    /**
     * Add questions
     *
     * @param \CPASimUSante\SimupollBundle\Entity\Question $questions
     * @return QuestionGroup
     */
    public function addQuestion(Question $questions)
    {
        $this->questions[] = $questions;

        return $this;
    }

    /**
     * Remove questions
     *
     * @param \CPASimUSante\SimupollBundle\Entity\Question $questions
     */
    public function removeQuestion(Question $questions)
    {
        $this->questions->removeElement($questions);
    }

    /**
     * Get questions
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getQuestions()
    {
        return $this->questions;
    }
}
