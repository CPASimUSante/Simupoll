<?php

namespace CPASimUSante\SimupollBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Period.
 *
 * @ORM\Table(name="cpasimusante__simupoll_period")
 * @ORM\Entity(repositoryClass="CPASimUSante\SimupollBundle\Repository\PeriodRepository")
 */
class Period
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
     * @ORM\ManyToOne(targetEntity="CPASimUSante\SimupollBundle\Entity\Simupoll")
     * @ORM\JoinColumn(name="simupoll_id", referencedColumnName="id",onDelete="CASCADE")
     */
    private $simupoll;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $start;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $stop;

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function setSimupoll(\CPASimUSante\SimupollBundle\Entity\Simupoll $simupoll)
    {
        $this->simupoll = $simupoll;
    }

    public function getSimupoll()
    {
        return $this->simupoll;
    }

    /**
     * Set start.
     *
     * @param \DateTime $start
     *
     * @return Period
     */
    public function setStart(\DateTime $start)
    {
        $this->start = $start;

        return $this;
    }

    /**
     * Get start.
     *
     * @return \DateTime
     */
    public function getStart()
    {
        return $this->start;
    }

    /**
     * Set title.
     *
     * @param string $title
     *
     * @return Period
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title.
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Set stop.
     *
     * @param \DateTime $stop
     *
     * @return Period
     */
    public function setStop(\DateTime $stop)
    {
        $this->stop = $stop;

        return $this;
    }

    /**
     * Get stop.
     *
     * @return \DateTime
     */
    public function getStop()
    {
        return $this->stop;
    }
}
