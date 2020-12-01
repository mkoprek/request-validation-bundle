<?php
declare(strict_types=1);

namespace MKoprek\RequestValidation\Resolver;

use MKoprek\RequestValidation\Request\RequestInterface;
use MKoprek\RequestValidation\Request\Exception\RequestValidationException;
use ReflectionClass;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Controller\ArgumentValueResolverInterface;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class RequestResolver implements ArgumentValueResolverInterface
{
    private ContainerInterface $container;
    private ValidatorInterface $validator;

    public function __construct(ContainerInterface $container, ValidatorInterface $validator)
    {
        $this->container = $container;
        $this->validator = $validator;
    }

    public function supports(Request $request, ArgumentMetadata $argument)
    {
        if (!class_exists($argument->getType())) {
            return false;
        }

        $reflection = new ReflectionClass($argument->getType());

        return $reflection->implementsInterface(RequestInterface::class);
    }

    public function resolve(Request $request, ArgumentMetadata $argument)
    {
        /** @var RequestInterface $requestBundle */
        $customRequest = $this->container->get($argument->getType());

        $customRequest->populate($request);

        $validationErrors = $this->validator->validate(
            $customRequest->getValidationData(),
            $customRequest->getValidationRules(),
        );

        if (count($validationErrors) > 0) {
            throw RequestValidationException::withError($validationErrors);
        }

        yield $customRequest;
    }
}
