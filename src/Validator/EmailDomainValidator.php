<?php


namespace App\Validator;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;

/**
 * Class EmailDomainValidator
 * @package App\Validator
 */
class EmailDomainValidator extends ConstraintValidator
{
    /**
     * @param mixed $value
     * @param Constraint $constraint
     */
    public function validate($value, Constraint $constraint)
    {
        $explodedEmail = explode('@', $value);
        $domain = array_pop($explodedEmail);

        if (!in_array($domain, $constraint->domains)) {
            $this->context->buildViolation($constraint->message)
                ->setParameter('%email%', $value)
                ->addViolation();
        }
    }

}