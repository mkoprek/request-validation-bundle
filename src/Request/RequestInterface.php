<?php
declare(strict_types=1);

namespace MKoprek\RequestValidation\Request;

use Symfony\Component\HttpFoundation\Request;

interface RequestInterface
{
    public function getValidationData(): array;
    public function getValidationRules(): array;
    public function populate(Request $request): void;
}
