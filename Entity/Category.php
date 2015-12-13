<?php
namespace CPASimUSante\SimupollBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Claroline\CoreBundle\Entity\User;

/**
 * CPASimUSante\SimupollBundle\Entity\Category
 *
 * @ORM\Entity(repositoryClass="CPASimUSante\SimupollBundle\Repository\CategoryRepository")
 * @ORM\Table(name="cpasimusante__simupoll_category")
 */
class Category
{
    /**
     * @var integer $id
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;
    /**
     * @var string $value
     *
     * @ORM\Column(name="value", type="string", length=255)
     */
    private $value;
    /**
     * @ORM\ManyToOne(targetEntity="Claroline\CoreBundle\Entity\User")
     */
    private $user;
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
     * Set value
     *
     * @param string $value
     */
    public function setValue($value)
    {
        $this->value = $value;
    }
    /**
     * Get value
     *
     * @return string
     */
    public function getValue()
    {
        return $this->value;
    }
    public function __toString()
    {
        return $this->value;
    }
    public function getUser()
    {
        return $this->user;
    }
    public function setUser(User $user)
    {
        $this->user = $user;
    }
}