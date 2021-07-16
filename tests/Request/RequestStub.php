<?php
declare(strict_types=1);

namespace Tests\MKoprek\RequestValidation\Request;

use MKoprek\RequestValidation\Request\AbstractRequest;
use Symfony\Component\Validator\Constraints as Assert;

class RequestStub extends AbstractRequest
{
    protected $id;
    protected $name;

    public function getId(): string
    {
        return $this->id;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getValidationRules(): array
    {
        return [
            new Assert\Collection([
                'id' => new Assert\Required([
                    new Assert\NotNull(),
                    new Assert\NotBlank(),
                    new Assert\Type('string'),
                ]),
                'name' => new Assert\Required([
                    new Assert\NotNull(),
                    new Assert\NotBlank(),
                    new Assert\Type('string'),
                ]),
            ]),
        ];
    }
}
