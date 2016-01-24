<?php
namespace CPASimUSante\SimupollBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Claroline\CoreBundle\Entity\User;
use Symfony\Bridge\Doctrine\Validator\Constraints as DoctrineAssert;

/*
 * CPASimUSante\SimupollBundle\Entity\Category
 *
 * @ORM\Entity(repositoryClass="CPASimUSante\SimupollBundle\Repository\CategoryRepository")
 * @ORM\Table(
 *      name="cpasimusante__simupoll_v1category",
 *      uniqueConstraints={
 *          @ORM\UniqueConstraint(name="category_unique_name_and_user", columns={"user_id", "name"})
 *      }
 * )
 * @DoctrineAssert\UniqueEntity({"name", "user"})
 */
class v1Category
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
     * @ORM\Column(name="name", type="string", length=255, nullable=false)
     */
    private $name;

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

    public function getName()
    {
        return $this->name;
    }

    public function setName($name)
    {
        $this->name = $name;
    }

    public function __toString()
    {
        return $this->id . '-' . $this->value;
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
