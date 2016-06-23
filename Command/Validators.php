<?php

namespace Grossum\CoreBundle\Command;

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\ConstraintViolationList;
use Symfony\Component\Validator\Validator\RecursiveValidator;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class Validators
{
    /** @var RecursiveValidator */
    private $validator;

    /** @var Email */
    private $emailConstraint;

    /** @var NotBlank */
    private $notBlankConstraint;

    /**
     * @param ValidatorInterface $validator
     */
    public function __construct(ValidatorInterface $validator)
    {
        $this->validator = $validator;
        $this->emailConstraint = new Email();
        $this->notBlankConstraint = new NotBlank();
    }

    /**
     * @param string $userName
     * @return string
     */
    public function validateUserName($userName)
    {
        $this->notBlankConstraint->message = 'grossum.core.user.login.not_blank';

        $errors = $this->validator->validate($userName, $this->notBlankConstraint);
        $this->handleError($errors);

        return $userName;
    }

    /**
     * @param string $userPassword
     * @return string
     */
    public function validateUserPassword($userPassword)
    {
        $this->notBlankConstraint->message = "grossum.core.user.password.not_blank";

        $errors = $this->validator->validate($userPassword, $this->notBlankConstraint);
        $this->handleError($errors);

        return $userPassword;
    }

    /**
     * @param string $userEmail
     * @return string
     */
    public function validateUserEmail($userEmail)
    {
        $this->notBlankConstraint->message = "grossum.core.user.email.not_blank";
        $this->emailConstraint->message = "grossum.core.user.email.valid";

        $errors = $this->validator->validate($userEmail, [
            $this->notBlankConstraint, $this->emailConstraint
        ]);
        $this->handleError($errors);

        return $userEmail;
    }

    private function handleError(ConstraintViolationList $errors)
    {
        if (isset($errors[0])) {
            throw new \InvalidArgumentException($errors[0]->getMessage());
        }
    }
}
