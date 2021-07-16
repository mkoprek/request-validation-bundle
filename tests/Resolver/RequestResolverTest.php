<?php
declare(strict_types=1);

namespace Tests\MKoprek\RequestValidation\Resolver;

use MKoprek\RequestValidation\Request\Exception\RequestValidationException;
use MKoprek\RequestValidation\Resolver\RequestResolver;
use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\ControllerMetadata\ArgumentMetadata;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Tests\MKoprek\RequestValidation\Request\RequestStub;

class RequestResolverTest extends TestCase
{
    /**
     * @test
     */
    function it_can_support()
    {
        $container = $this->createMock(ContainerInterface::class);
        $validator = $this->createMock(ValidatorInterface::class);

        $requestResolver = new RequestResolver($container, $validator);
        $arguments = $this->createMock(ArgumentMetadata::class);
        $arguments->method('getType')
                  ->willReturn(RequestStub::class);

        $this->assertTrue($requestResolver->supports(new Request(), $arguments));
    }

    /**
     * @test
     */
    function it_cant_support_if_class_does_not_exists()
    {
        $container = $this->createMock(ContainerInterface::class);
        $validator = $this->createMock(ValidatorInterface::class);

        $requestResolver = new RequestResolver($container, $validator);
        $arguments = $this->createMock(ArgumentMetadata::class);
        $arguments->method('getType')
                  ->willReturn('NonExistingClass::class');

        $this->assertFalse($requestResolver->supports(new Request(), $arguments));
    }

    /**
     * @test
     */
    function it_cant_support_if_argument_type_is_null_or_string()
    {
        $container = $this->createMock(ContainerInterface::class);
        $validator = $this->createMock(ValidatorInterface::class);

        $requestResolver = new RequestResolver($container, $validator);
        $arguments = $this->createMock(ArgumentMetadata::class);
        $arguments->method('getType')
                  ->willReturn(null);

        $this->assertFalse($requestResolver->supports(new Request(), $arguments));
    }

    /**
     * @test
     */
    function it_can_resolve()
    {
        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')
                  ->willReturn(new RequestStub());

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->method('validate')
                  ->willReturn([]);

        $requestResolver = new RequestResolver($container, $validator);

        $arguments = $this->createMock(ArgumentMetadata::class);
        $arguments->method('getType')
                  ->willReturn(RequestStub::class);

        $this->assertTrue($requestResolver->supports(new Request(), $arguments));

        $return = $requestResolver->resolve(new Request(), $arguments);

        $this->assertIsIterable($return);
        $this->assertCount(1, $return);
    }

    /**
     * @test
     */
    public function it_throw_exception_when_resolve()
    {
        $this->expectException(RequestValidationException::class);

        $container = $this->createMock(ContainerInterface::class);
        $container->method('get')
                  ->willReturn(new RequestStub());

        $validationError = $this->createMock(ConstraintViolationListInterface::class);
        $validationError->method('count')
                        ->willReturn(123);

        $validator = $this->createMock(ValidatorInterface::class);
        $validator->method('validate')
                  ->willReturn($validationError);

        $requestResolver = new RequestResolver($container, $validator);

        $arguments = $this->createMock(ArgumentMetadata::class);
        $arguments->method('getType')
                  ->willReturn(RequestStub::class);

        $this->assertTrue($requestResolver->supports(new Request(), $arguments));

        $return = $requestResolver->resolve(new Request(), $arguments);
        $return->getReturn();
    }
}
