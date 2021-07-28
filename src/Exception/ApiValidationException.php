<?php
declare(strict_types=1);

namespace MKoprek\RequestValidation\Exception;

class ApiValidationException extends ApiProblemException
{
    private const MESSAGE = 'Validation Exception';

    public function __construct(
        int $statusCode,
        ?string $message = '',
        iterable $details = [],
        \Throwable $previous = null,
        array $headers = [],
        ?int $code = 0
    ) {
        parent::__construct(422, self::MESSAGE, $details, $previous, $headers, $code);
    }

    public static function withDetails(iterable $details)
    {
        return new self(422, self::MESSAGE, array(...$details));
    }
}
