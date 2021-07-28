<?php
declare(strict_types=1);

namespace MKoprek\RequestValidation\Exception;

use Symfony\Component\HttpKernel\Exception\HttpException;

class ApiProblemException extends HttpException
{
    private array $details;

    public function __construct(
        int $statusCode,
        ?string $message = '',
        array $details = [],
        \Throwable $previous = null,
        array $headers = [],
        ?int $code = 0
    ) {
        parent::__construct($statusCode, $message, $previous, $headers, $code);
        $this->details = $details;
    }

    public function getDetails(): array
    {
        return $this->details;
    }
}
