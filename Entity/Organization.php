<?php

namespace CPASimUSante\SimupollBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use CPASimUSante\SimupollBundle\Entity\Simupoll;

/**
 * Organization
 *
 * @ORM\Table(name="cpasimusante__simupoll_organization")
 * @ORM\Entity()
 */
class Organization
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
     * @var string
     *
     * @ORM\Column(name="choice", type="string", length=255, nullable=false)
     */
    protected $choice;

    /**
     * @var string
     *
     * @ORM\Column(name="choice_data", type="text")
     */
    protected $choiceData;

    /**
     * @ORM\ManyToOne(targetEntity="CPASimUSante\SimupollBundle\Entity\Simupoll")
     * @ORM\JoinColumn(name="simupoll_id", referencedColumnName="id", onDelete="CASCADE")
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
     * Set choice
     *
     * @param string $choice
     *
     * @return Organization
     */
    public function setChoice($choice)
    {
        $this->choice = $choice;

        return $this;
    }

    /**
     * Get choice
     *
     * @return string
     */
    public function getChoice()
    {
        return $this->choice;
    }

    /**
     * Set choiceData
     *
     * @param string $choiceData
     *
     * @return Organization
     */
    public function setChoiceData($choiceData)
    {
        $this->choiceData = $choiceData;

        return $this;
    }

    /**
     * Get choiceData
     *
     * @return string
     */
    public function getChoiceData()
    {
        return $this->choiceData;
    }

    public function getSimupoll()
    {
        return $this->simupoll;
    }

    public function setSimupoll(Simupoll $simupoll)
    {
        $this->simupoll = $simupoll;
    }
}

