<?php

namespace CPASimUSante\SimupollBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Claroline\CoreBundle\Entity\User;
use CPASimUSante\SimupollBundle\Entity\Simupoll;

/**
 * Statmanage
 *
 * @ORM\Table(name="cpasimusante__simupoll_statmanage")
 * @ORM\Entity(repositoryClass="CPASimUSante\SimupollBundle\Repository\StatmanageRepository")
 */
class Statmanage
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
     * @ORM\Column(name="userlist", type="string", length=255, nullable=true)
     */
    protected $userList;

    /**
     * @var string
     *
     * @ORM\Column(name="categorylist", type="string", length=255, nullable=true)
     */
    protected $categoryList;

    /**
     * @ORM\ManyToOne(targetEntity="Claroline\CoreBundle\Entity\User")
     */
    private $user;

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
     * Set userList
     *
     * @param string $userList
     *
     * @return Statmanage
     */
    public function setUserList($userList)
    {
        $this->userList = $userList;

        return $this;
    }

    /**
     * Get userList
     *
     * @return string
     */
    public function getUserList()
    {
        return $this->userList;
    }

    /**
     * Set userList
     *
     * @param string $categoryList
     *
     * @return Statmanage
     */
    public function setCategoryList($categoryList)
    {
        $this->categoryList = $categoryList;

        return $this;
    }

    /**
     * Get categoryList
     *
     * @return string
     */
    public function getcategoryList()
    {
        return $this->categoryList;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
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

