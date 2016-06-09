<?php

namespace Grossum\CoreBundle\Command;

use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\NotBlank;
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
        $errors = $this->validator->validate($userName, $this->notBlankConstraint);

        if ($errors->count() !== 0) {
            throw new \InvalidArgumentException(sprintf('You have to enter a userName', $userName));
        }

        return $userName;
    }

    /**
     * @param string $userPassword
     * @return string
     */
    public function validateUserPassword($userPassword)
    {
        $errors = $this->validator->validate($userPassword, $this->notBlankConstraint);

        if ($errors->count() !== 0) {
            throw new \InvalidArgumentException(sprintf('You have to enter a userPassword', $userPassword));
        }

        return $userPassword;
    }

    /**
     * @param string $userEmail
     * @return string
     */
    public function validateUserEmail($userEmail)
    {
        $errors = $this->validator->validate($userEmail, [
            $this->emailConstraint, $this->notBlankConstraint
        ]);

        if ($errors->count() !== 0) {
            throw new \InvalidArgumentException(sprintf('You entered a wrong email address (%s)', $userEmail));
        }

        return $userEmail;
    }
}
