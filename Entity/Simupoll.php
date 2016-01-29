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
use Claroline\CoreBundle\Entity\Resource\AbstractResource;

/**
 * @ORM\Table(name="cpasimusante__simupoll")
 * @ORM\Entity(repositoryClass="CPASimUSante\SimupollBundle\Repository\SimupollRepository")
 */
class Simupoll extends AbstractResource
{
    /**
     * @var string $title
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var Questions[]
     *
     * @ORM\OneToMany(targetEntity="CPASimUSante\SimupollBundle\Entity\Question", mappedBy="simupoll", cascade={"all"})
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
     * Set title
     *
     * @param string $title
     */
    public function setTitle($title)
    {
        $this->title = $title;
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
     * Add question
     * @param \CPASimUSante\SimupollBundle\Entity\Question $question
     *
     * @return Simupoll
     */
    public function addQuestion(\CPASimUSante\SimupollBundle\Entity\Question $question)
    {
        /*       $this->items[] = $item;
               //$item->setItemselector($this);
               return $this;
       */
        $question->setSimupoll($this);

        $this->questions->add($question);
    }

    /**
     * Remove question
     *
     * @param \CPASimUSante\SimupollBundle\Entity\Question $question
     */
    public function removeQuestion(\CPASimUSante\SimupollBundle\Entity\Question $question)
    {
        $this->questions->removeElement($question);
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
