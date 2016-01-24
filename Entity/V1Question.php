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
use CPASimUSante\SimupollBundle\Entity\QuestionGroup;

/*
 * @ORM\Table(name="cpasimusante__v1question")
 * @ORM\Entity(repositoryClass="CPASimUSante\SimupollBundle\Repository\QuestionRepository")
 */
class V1Question
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
     * @var QuestionGroup
     *
     * @ORM\ManyToOne(targetEntity="CPASimUSante\SimupollBundle\Entity\QuestionGroup", inversedBy="questions")
     * @ORM\JoinColumn(name="questiongroup_id", referencedColumnName="id")
     */
    protected $questiongroup;

    /**
     * @ORM\ManyToOne(targetEntity="CPASimUSante\SimupollBundle\Entity\Category")
     */
    private $category;

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
     * Set questiongroup
     *
     * @param \CPASimUSante\SimupollBundle\Entity\QuestionGroup $questiongroup
     * @return Question
     */
    public function setQuestiongroup(QuestionGroup $questiongroup = null)
    {
        $this->questiongroup = $questiongroup;

        return $this;
    }

    /**
     * Get questiongroup
     *
     * @return \CPASimUSante\SimupollBundle\Entity\QuestionGroup 
     */
    public function getQuestiongroup()
    {
        return $this->questiongroup;
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
}
