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
use CPASimUSante\SimupollBundle\Entity\Category;
use CPASimUSante\SimupollBundle\Entity\Simupoll;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * @ORM\Table(name="cpasimusante__simupoll_question")
 * @ORM\Entity(repositoryClass="CPASimUSante\SimupollBundle\Repository\QuestionRepository")
 */
class Question
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
     * @var integer $orderq
     *
     * @ORM\Column(name="orderq", type="integer", options={"default" = 0})
     * @Assert\Type(type = "numeric")
     * @Assert\NotBlank(
     *   message = "The order should not be blank and be a number"
     * )
     */
    private $orderq;

    /**
     * @var Simupoll
     *
     * @ORM\ManyToOne(targetEntity="CPASimUSante\SimupollBundle\Entity\Simupoll", inversedBy="questions")
     * @ORM\JoinColumn(name="simupoll_id", referencedColumnName="id")
     */
    protected $simupoll;

    /**
     * Category of question
     *
     * @ORM\ManyToOne(targetEntity="CPASimUSante\SimupollBundle\Entity\Category")
     */
    private $category;

    /**
     * @var Propositions[]
     *
     * @ORM\OneToMany(targetEntity="CPASimUSante\SimupollBundle\Entity\Proposition", mappedBy="question", cascade={"all"})
     */
    protected $propositions;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->propositions = new ArrayCollection();
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
     * @return Question
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
     * Set order
     *
     * @param integer $order
     * @return Question
     */
    public function setOrderq($orderq)
    {
        $this->orderq = $orderq;

        return $this;
    }

    /**
     * Get order
     *
     * @return integer
     */
    public function getOrderq()
    {
        return $this->orderq;
    }

    /**
     * Set category
     *
     * @param \CPASimUSante\SimupollBundle\Entity\Category $category
     * @return Question
     */
    public function setCategory(Category $category = null)
    {
        $this->category = $category;

        return $this;
    }

    /**
     * Get category
     *
     * @return \CPASimUSante\SimupollBundle\Entity\Category 
     */
    public function getCategory()
    {
        return $this->category;
    }

    /**
     * Add proposition
     *
     * @param \CPASimUSante\SimupollBundle\Entity\Proposition $proposition
     *
     * @return Question
     */
    public function addProposition(\CPASimUSante\SimupollBundle\Entity\Proposition $proposition)
    {
        $this->propositions[] = $proposition;
        $proposition->setQuestion($this);
        return $this;
       /*
        $proposition->setQuestion($this);
        $this->propositions->add($proposition);
        */
    }

    /**
     * Remove proposition
     *
     * @param \CPASimUSante\SimupollBundle\Entity\Proposition $proposition
     */
    public function removeProposition(\CPASimUSante\SimupollBundle\Entity\Proposition $proposition)
    {
        $this->propositions->removeElement($proposition);
    }

    /**
     * Get propositions
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getPropositions()
    {
        return $this->propositions;
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
     * Set simupoll
     *
     * @param \CPASimUSante\SimupollBundle\Entity\Question $question
     *
     * @return Question
     */
    public function setSimupoll(Simupoll $simupoll = null)
    {
        $this->simupoll = $simupoll;

        return $this;
    }
}
