<?php

namespace CPASimUSante\SimupollBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
/**
 * Statcategorygroup.
 *
 * @ORM\Table(name="cpasimusante__simupoll_statcategorygroup")
 * @ORM\Entity(repositoryClass="CPASimUSante\SimupollBundle\Repository\StatcategorygroupRepository")
 */
class Statcategorygroup
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
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255)
     */
    private $title;

    /**
     * @var string
     *
     * @ORM\Column(name="categorygroup", type="string", length=255, nullable=true)
     */
    private $group;

    /**
     * @var Statmanage
     *
     * @ORM\ManyToOne(targetEntity="CPASimUSante\SimupollBundle\Entity\Statmanage", inversedBy="statcategorygroups")
     * @ORM\JoinColumn(name="statmanage_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $statmanage;

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
     * Set title.
     *
     * @param string $title
     *
     * @return Statcategorygroup
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
     * Set group.
     *
     * @param string $group
     *
     * @return Statcategorygroup
     */
    public function setGroup($group)
    {
        $this->group = $group;

        return $this;
    }

    /**
     * Get group.
     *
     * @return string
     */
    public function getGroup()
    {
        return $this->group;
    }

    /**
     * Get statmanage.
     *
     * @return \CPASimUSante\SimupollBundle\Entity\Statmanage
     */
    public function getStatmanage()
    {
        return $this->statmanage;
    }

    /**
     * Set statmanage.
     *
     * @param \CPASimUSante\SimupollBundle\Entity\Statmanage $statmanage
     *
     * @return Statcategorygroup
     */
    public function setStatmanage(Statmanage $statmanage = null)
    {
        $this->statmanage = $statmanage;

        return $this;
    }
}
