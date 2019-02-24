<?php

namespace App\Helpers;

use Symfony\Component\Validator\ConstraintViolationListInterface;

class ErrorHelper
{
    /**
     * @param ConstraintViolationListInterface $errors
     * @return array
     */
    public function prepareResponse(ConstraintViolationListInterface $errors)
    {
        $formErrors = [];
        foreach ($errors as $error) {
            $formErrors[$error->getPropertyPath()] = $error->getMessage();
        }

        return $formErrors;
    }
}