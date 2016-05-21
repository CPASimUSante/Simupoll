<?php

namespace CPASimUSante\SimupollBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Claroline\CoreBundle\Entity\User;
use Doctrine\Common\Collections\ArrayCollection;

/**
 * Statmanage.
 *
 * @ORM\Table(name="cpasimusante__simupoll_statmanage")
 * @ORM\Entity(repositoryClass="CPASimUSante\SimupollBundle\Repository\StatmanageRepository")
 */
class Statmanage
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
     * @ORM\Column(name="userlist", type="string", length=255, nullable=true)
     */
    protected $userList;

    /**
     * @var statcategorygroups[]
     *
     * @ORM\OneToMany(targetEntity="CPASimUSante\SimupollBundle\Entity\Statcategorygroup", mappedBy="statmanage", cascade={"all"})
     */
    protected $statcategorygroups;

    /**
     * @var string
     *
     * @ORM\Column(name="categorylist", type="string", length=255, nullable=true)
     */
    protected $categoryList;

    /**
     * @var string
     *
     * @ORM\Column(name="completecategorylist", type="text", nullable=true)
     */
    protected $completeCategoryList;

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
     * Class constructor.
     */
    public function __construct()
    {
        $this->statcategorygroups = new ArrayCollection();
    }

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
     * Set userList.
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
     * Get userList.
     *
     * @return string
     */
    public function getUserList()
    {
        return $this->userList;
    }

    /**
     * Set categoryList.
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
     * Get categoryList.
     *
     * @return string
     */
    public function getCategoryList()
    {
        return $this->categoryList;
    }

    /**
     * Set completeCategoryList.
     *
     * @param string $completeCategoryList
     *
     * @return Statmanage
     */
    public function setCompleteCategoryList($completeCategoryList)
    {
        $this->completeCategoryList = $completeCategoryList;

        return $this;
    }

    /**
     * Get completeCategoryList.
     *
     * @return string
     */
    public function getCompleteCategoryList()
    {
        return $this->completeCategoryList;
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

    /**
     * Add statcategorygroup.
     *
     * @param \CPASimUSante\SimupollBundle\Entity\Statcategorygroup $statcategorygroup
     *
     * @return Statmanage
     */
    public function addStatcategorygroup(\CPASimUSante\SimupollBundle\Entity\Statcategorygroup $statcategorygroup)
    {
        $this->statcategorygroups[] = $statcategorygroup;
        $statcategorygroup->setStatmanage($this);

        return $this;
    }

    /**
     * Remove statcategorygroup.
     *
     * @param \CPASimUSante\SimupollBundle\Entity\Statcategorygroup $statcategorygroup
     */
    public function removeStatcategorygroup(\CPASimUSante\SimupollBundle\Entity\Statcategorygroup $statcategorygroup)
    {
        $this->statcategorygroups->removeElement($statcategorygroup);
    }

    /**
     * Get statcategorygroups.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getStatcategorygroups()
    {
        return $this->statcategorygroups;
    }
}
