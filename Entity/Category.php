<?php
namespace CPASimUSante\SimupollBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Doctrine\Common\Collections\ArrayCollection;
use Claroline\CoreBundle\Entity\User;
use CPASimUSante\SimupollBundle\Entity\Simupoll;
use Gedmo\Mapping\Annotation as Gedmo;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

/**
 * Simupoll categories
 *
 * @ORM\Entity(repositoryClass="CPASimUSante\SimupollBundle\Repository\CategoryRepository")
 * @ORM\Table(
 *      name="cpasimusante__simupoll_category",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint(name="category_unique_name_and_simupoll", columns={"simupoll_id", "user_id", "name"})
 *      }
 * )
 * @DoctrineAssert\UniqueEntity({"name", "simupoll", "user"})
 * @Gedmo\Tree(type="nested")
 */
class Category
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * name of the category
     * @var string $value
     *
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @Gedmo\TreeLeft
     * @ORM\Column(name="lft", type="integer")
     */
    private $lft;

    /**
     * @Gedmo\TreeLevel
     * @ORM\Column(name="lvl", type="integer")
     */
    private $lvl;

    /**
     * @Gedmo\TreeRight
     * @ORM\Column(name="rgt", type="integer")
     */
    private $rgt;

    /**
     * @Gedmo\TreeRoot
     * @ORM\Column(name="root", type="integer", nullable=true)
     */
    private $root;

    /**
     * @Gedmo\TreeParent
     * @ORM\ManyToOne(
     *     targetEntity="CPASimUSante\SimupollBundle\Entity\Category",
     *     inversedBy="children"
     * )
     * @ORM\JoinColumn(name="parent_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $parent;

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
     * @ORM\OneToMany(
     *     targetEntity="CPASimUSante\SimupollBundle\Entity\Category",
     *     mappedBy="parent",
     * )
     * @ORM\OrderBy({"lft" = "ASC"})
     */
    protected $children;

    /**
     * propoerty used in hierarchy display, like selectbox
     */
    private $indentedName;

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

    /**
     * Returns the resource id.
     *
     * @return integer
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the resource id.
     * Required by the ResourceController when it creates a fictionnal root
     *
     * @param integer $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    /**
     * Returns the children resource instances.
     *
     * @return \Doctrine\Common\ArrayCollection|Category[]
     */
    public function getChildren()
    {
        return $this->children;
    }

    /**
     * Returns the parent category.
     *
     * @return \CPASimUSante\SimupollBundle\Entity\Category
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Sets the parent category.
     *
     * @param \CPASimUSante\SimupollBundle\Entity\Category $parent
     */
    public function setParent(\CPASimUSante\SimupollBundle\Entity\Category $parent = null)
    {
        $this->parent = $parent;
    }

    /**
     * Return the lvl value of the resource in the tree.
     *
     * @return integer
     */
    public function getLvl()
    {
        return $this->lvl;
    }

    public function getSimupoll()
    {
        return $this->simupoll;
    }

    public function setSimupoll(Simupoll $simupoll)
    {
        $this->simupoll = $simupoll;
    }

    public function getLft()
    {
        return $this->lft;
    }

    public function getUser()
    {
        return $this->user;
    }

    public function setUser(User $user)
    {
        $this->user = $user;
    }
    /**
     * allows hierachy display
     * @return string
     */
    public function __toString()
    {
        return $this->getName();
    }

    /**
     * allows hierachy display
     * @return string
     */
    public function getIndentedName()
    {
        return str_repeat("----", $this->lvl) . $this->name;
        //return str_repeat($this->getParent()." > ", $this->lvl) . $this->name;
    }
}
