<?php
namespace CPASimUSante\SimupollBundle\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Claroline\CoreBundle\Entity\User;

/**
 *
 * @ORM\Entity(repositoryClass="CPASimUSante\SimupollBundle\Repository\TagRepository")
 * @ORM\Table(name="cpasimusante__simupoll_tags")
 */
class Tag
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
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

    /**
     * @ORM\ManyToOne(targetEntity="Claroline\CoreBundle\Entity\User")
     */
    private $user;

    /**
     * @ORM\ManyToOne(targetEntity="Tag", inversedBy="children")
     * @ORM\JoinColumn(name="parent_tag_id", referencedColumnName="id")
     */
    private $parent;

    /**
     * @var ArrayCollection
     * @ORM\OneToMany(targetEntity="Tag", mappedBy="parent")
     */
    private $children;

    /**
     * @ORM\Column(name="lvl", type="integer", nullable=false)
     */

    public function __construct()
    {
        $this->children = new ArrayCollection();
    }

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
     * Set name
     *
     * @param string $name
     * @return Tag
     */
    public function setName($name)
    {
        $this->name = $name;

        return $this;
    }

    /**
     * Get name
     *
     * @return string 
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * Set user
     *
     * @param \Claroline\CoreBundle\Entity\User $user
     * @return Tag
     */
    public function setUser(\Claroline\CoreBundle\Entity\User $user = null)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Claroline\CoreBundle\Entity\User 
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set parent
     *
     * @param \CPASimUSante\SimupollBundle\Entity\Tag $parent
     * @return Tag
     */
    public function setParent(\CPASimUSante\SimupollBundle\Entity\Tag $parent = null)
    {
        $this->parent = $parent;

        return $this;
    }

    /**
     * Get parent
     *
     * @return \CPASimUSante\SimupollBundle\Entity\Tag 
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Add children
     *
     * @param \CPASimUSante\SimupollBundle\Entity\Tag $children
     * @return Tag
     */
    public function addChild(\CPASimUSante\SimupollBundle\Entity\Tag $children)
    {
        $this->children[] = $children;

        return $this;
    }

    /**
     * Remove children
     *
     * @param \CPASimUSante\SimupollBundle\Entity\Tag $children
     */
    public function removeChild(\CPASimUSante\SimupollBundle\Entity\Tag $children)
    {
        $this->children->removeElement($children);
    }

    /**
     * Get children
     *
     * @return \Doctrine\Common\Collections\Collection 
     */
    public function getChildren()
    {
        return $this->children;
    }
}
