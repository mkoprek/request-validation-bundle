<?php
declare(strict_types=1);

namespace MKoprek\RequestValidation\Request\Exception;

use Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException;
use Symfony\Component\Validator\ConstraintViolationListInterface;

class RequestValidationException extends UnprocessableEntityHttpException
{
    public const MESSAGE = 'Request validation error';

    private ConstraintViolationListInterface $errors;

    public function setErrors(ConstraintViolationListInterface $errors): void
    {
        $this->errors = $errors;
    }

    public function getErrors(): ConstraintViolationListInterface
    {
        return $this->errors;
    }

    public static function withError(ConstraintViolationListInterface $errors)
    {
        $self = new self(self::MESSAGE);
        $self->setErrors($errors);

        return $self;
    }
}
