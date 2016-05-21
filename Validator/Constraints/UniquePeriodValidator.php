<?php

namespace CPASimUSante\SimupollBundle\Validator\Constraints;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Doctrine\ORM\EntityManager;
use JMS\DiExtraBundle\Annotation as DI;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

/**
 * @DI\Validator("unique_period_validator")
 */
class UniquePeriodValidator extends ConstraintValidator
{
    private $em;
    private $tokenStorage;

    /**
     * @DI\InjectParams({
     *     "em"                    = @DI\Inject("doctrine.orm.entity_manager"),
     *     "tokenStorage"          = @DI\Inject("security.token_storage")
     * })
     */
    public function __construct(
        EntityManager $em,
        TokenStorageInterface $tokenStorage
    ) {
        $this->em = $em;
        $this->tokenStorage = $tokenStorage;
    }

    public function validate($date, Constraint $constraint)
    {
        $user = $this->tokenStorage->getToken()->getUser();
        $uid = $user->getId();
        $period = $this->em
            ->getRepository('CPASimUSanteSimupollBundle:Period')
            ->findOneContaining($uid, $date);

        if ($period) {
            $this->context->addViolation($constraint->message, array('{{ date }}' => $date));
        }
    }
}
