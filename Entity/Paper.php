<?php

namespace CPASimUSante\SimupollBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Paper
 *
 * @ORM\Table(name="cpasimusante__simupoll_paper")
 * @ORM\Entity(repositoryClass="CPASimUSante\SimupollBundle\Repository\PaperRepository")
 */
class Paper
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
     * @var \DateTime
     *
     * @ORM\Column(name="start", type="datetime")
     */
    private $start;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="end", type="datetime", nullable=true)
     */
    private $end;

    /**
     * @var integer
     *
     * @ORM\Column(name="num_paper", type="integer")
     */
    private $numPaper;

    /**
     * @ORM\ManyToOne(targetEntity="Claroline\CoreBundle\Entity\User")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="CPASimUSante\SimupollBundle\Entity\Simupoll")
     * @ORM\JoinColumn(name="simupoll_id", referencedColumnName="id",onDelete="CASCADE")
     */
    private $simupoll;

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
     * Set start
     *
     * @param \DateTime $start
     *
     * @return Paper
     */
    public function setStart($start)
    {
        $this->start = $start;

        return $this;
    }

    /**
     * Get start
     *
     * @return \DateTime
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Set end
     *
     * @param \DateTime $end
     *
     * @return Paper
     */
    public function setEnd($end)
    {
        $this->end = $end;

        return $this;
    }

    /**
     * Get end
     *
     * @return \DateTime
     */
    public function getEnd()
    {
        return $this->end;
    }

    /**
     * Set numPaper
     *
     * @param integer $numPaper
     *
     * @return Paper
     */
    public function setNumPaper($numPaper)
    {
        $this->numPaper = $numPaper;

        return $this;
    }

    /**
     * Get numPaper
     *
     * @return integer
     */
    public function getNumPaper()
    {
        return $this->numPaper;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser(\Claroline\CoreBundle\Entity\User $user)
    {
        $this->user = $user;
    }

    public function getSimupoll()
    {
        return $this->simupoll;
    }

    public function setSimupoll(\CPASimUSante\SimupollBundle\Entity\Simupoll $simupoll)
    {
        $this->simupoll = $simupoll;
    }
}

