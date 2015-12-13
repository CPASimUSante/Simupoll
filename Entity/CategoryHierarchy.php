<?php
namespace CPASimUSante\SimupollBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass="CPASimUSante\SimupollBundle\Repository\CategoryHierarchyRepository")
 * @ORM\Table(name="cpasimusante__simupoll_category_hierarchy")
 */
class CategoryHierarchy
{
    /**
     * @ORM\Id
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @ORM\ManyToOne(
     *     targetEntity="CPASimUSante\SimupollBundle\Entity\Category"
     * )
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    protected $parent;

    /**
     * @ORM\Column(type="integer")
     */
    protected $level;

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
     * Set level
     *
     * @param integer $level
     * @return CategoryHierarchy
     */
    public function setLevel($level)
    {
        $this->level = $level;

        return $this;
    }

    /**
     * Get level
     *
     * @return integer 
     */
    public function getLevel()
    {
        return $this->level;
    }

    /**
     * Set parent
     *
     * @param \CPASimUSante\SimupollBundle\Entity\Category $parent
     * @return CategoryHierarchy
     */
    public function setParent(\CPASimUSante\SimupollBundle\Entity\Category $parent)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \CPASimUSante\SimupollBundle\Entity\Category 
     */
    public function getParent()
    {
        return $this->parent;
    }
}
