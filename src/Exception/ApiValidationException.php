<?php
declare(strict_types=1);

namespace MKoprek\RequestValidation\Exception;

class ApiValidationException extends ApiProblemException
{
    public const MESSAGE = 'Validation Exception';

    public function __construct(
        int $statusCode,
        ?string $message = '',
        iterable $details = [],
    ) {
        parent::__construct($statusCode, $message, $details);
    }

    public static function withDetails(array $details): self
    {
        return new self(422, self::MESSAGE, $details);
    }
}
