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
use CPASimUSante\SimupollBundle\Entity\QuestionGroup;

/**
 * @ORM\Table(name="cpasimusante__simupoll")
 * @ORM\Entity(repositoryClass="CPASimUSante\SimupollBundle\Repository\SimupollRepository")
 */
class Simupoll extends AbstractResource
{
    /**
     * @var $questiongroups[]
     *
     * @ORM\OneToMany(targetEntity="CPASimUSante\SimupollBundle\Entity\QuestionGroup", mappedBy="simupoll", cascade={"all"})
     */
    protected $questiongroups;

    /**
     * Class constructor
     */
    public function __construct()
    {
        $this->questiongroups = new ArrayCollection();
    }

    /**
     * Add questiongroups
     *
     * @param \CPASimUSante\SimupollBundle\Entity\QuestionGroup $questiongroups
     * @return Simupoll
     */
    public function addQuestiongroup(QuestionGroup $questiongroups)
    {
        $this->questiongroups[] = $questiongroups;

        return $this;
    }

    /**
     * Remove questiongroups
     *
     * @param \CPASimUSante\SimupollBundle\Entity\QuestionGroup $questiongroups
     */
    public function removeQuestiongroup(QuestionGroup $questiongroups)
    {
        $this->questiongroups->removeElement($questiongroups);
    }

    /**
     * Get questiongroups
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getQuestiongroups()
    {
        return $this->questiongroups;
    }
}
