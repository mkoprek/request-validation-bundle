# API Request Validation Bundle
[![Build Status](https://travis-ci.com/mkoprek/request-validation-bundle.svg?branch=main)](https://travis-ci.com/mkoprek/request-validation-bundle)
[![codecov](https://codecov.io/gh/mkoprek/request-validation-bundle/branch/main/graph/badge.svg?token=LF5FSUH6SC)](https://codecov.io/gh/mkoprek/request-validation-bundle)

This is a simple library for easier and cleaner handling requests. You can simply define incoming payload and validation rules with it. Also you can simply cast incoming data for example to int value.

## Installation
```bash
composer require mkoprek/request-validation-bundle
```

## Usage

You need to create class which is extending [AbstractRequest](https://github.com/mkoprek/request-validation-bundle/blob/main/src/Request/AbstractRequest.php), then:
* Create field you want to get from request as a class properties
* Add validation rules as a Symfony Constraints to `getValidationRules()` method
* Add casting variables to other types or object (ex. Uuid)

Request:
```php
<?php
declare(strict_types=1);

use MKoprek\RequestValidation\Request\AbstractRequest;
use Symfony\Component\Uid\Uuid;
use Symfony\Component\Validator\Constraints as Assert;

class UserCreateRequest extends AbstractRequest
{
    protected string $id;
    protected ?string $name;

    public function getId(): Uuid
    {
        return Uuid::fromString($this->id);
    }
    
    public function getName(): ?string
    {
        return $this->name;
    }

    /**
     * @return array<Assert\Collection>
     */
    public function getValidationRules(): array
    {
        return [
            new Assert\Collection([
                'id' => new Assert\Required([
                    new Assert\NotNull(),
                    new Assert\NotBlank(),
                    new Assert\Uuid(),
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

```
Controller:
```php
<?php
declare(strict_types=1);

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/api')]
class UserCreateController
{
    #[Route('/users', name: 'users.create', methods: 'POST')]
    public function post(UserCreateRequest $request): JsonResponse
    {
        $id = $request->getId();
        $name = $request->getName();

        $this->commandBus->handle(
            CreateUserCommand($request->getId(), $request->getName())
        );

        return new JsonResponse(['id' => $id->toRfc4122()], Response::HTTP_CREATED);
    }
}
```

## Validation

Validation is done automatically before request is parsed by controller. If there will be any validation error json with status of field will be returned:

```json
{
    "status": 422,
    "message": "Request validation error",
    "details": [
        {
            "field": "[id]",
            "error": "This field is missing."
        },
        {
            "field": "[name]",
            "error": "This field is missing."
        },
        {
            "field": "[name2]",
            "error": "This field was not expected."
        }
    ]
```

If exception will be thrown, then json with status code and message will be returned:

```json
{
"status": 500,
"message": "Attempted to call an undefined method named 'notExists' of class 'UserCreateRequest'."
}
```

License
-------
MIT

Author Information
------------------
Created by Maciej Koprek (mkoprek) 2021
