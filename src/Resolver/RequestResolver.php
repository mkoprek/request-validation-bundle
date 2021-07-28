<?php
declare(strict_types=1);

namespace MKoprek\RequestValidation\Resolver;

use MKoprek\RequestValidation\Exception\ApiValidationException;
use MKoprek\RequestValidation\Request\Exception\RequestValidationException;
use MKoprek\RequestValidation\Request\RequestInterface;
use ReflectionClass;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\ConstraintViolationInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestResolver implements ArgumentValueResolverInterface
{
    public function __construct(private ContainerInterface $container, private ValidatorInterface $validator)
    {
    }

    /**
     * @return bool
     */
    public function supports(Request $request, ArgumentMetadata $argument)
    {
        if ($argument->getType() === null || $argument->getType() === '') {
            return false;
        }

        if (!class_exists($argument->getType())) {
            return false;
        }

        $reflection = new ReflectionClass($argument->getType());

        return $reflection->implementsInterface(RequestInterface::class);
    }

    /**
     * @return \Generator
     */
    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        /** @var RequestInterface $customRequest */
        $customRequest = $this->container->get($argument->getType());

        $customRequest->populate($request);

        $validationErrors = $this->validator->validate(
            $customRequest->getValidationData(),
            $customRequest->getValidationRules(),
        );

        if (count($validationErrors) > 0) {
            $array = [];

            /** @var ConstraintViolationInterface $validationError */
            foreach($validationErrors as $validationError) {
                $array[] = [
                    'field' => $validationError->getPropertyPath(),
                    'error' => $validationError->getMessage(),
                ];
            }

            throw ApiValidationException::withDetails($array);
        }

        yield $customRequest;
    }
}
