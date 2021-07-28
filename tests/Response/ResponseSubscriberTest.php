<?php
declare(strict_types=1);

namespace Tests\MKoprek\RequestValidation\Response;

use Exception;
use MKoprek\RequestValidation\Exception\ApiValidationException;
use MKoprek\RequestValidation\Request\Exception\RequestValidationException;
use MKoprek\RequestValidation\Response\ResponseSubscriber;
use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\Validator\ConstraintViolation;
use Symfony\Component\Validator\ConstraintViolationList;

class ResponseSubscriberTest extends TestCase
{
    /**
     * @test
     */
    public function it_handle_kernel_exception()
    {
        $exceptionMessage = md5((string) microtime(true));
        $exceptionCode = rand(400, 500);

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $exceptionEvent = new ExceptionEvent(
            $kernelMock,
            new Request(),
            HttpKernelInterface::MAIN_REQUEST,
            new Exception($exceptionMessage, $exceptionCode)
        );

        $kernelException = new ResponseSubscriber();
        $kernelException->onKernelException($exceptionEvent);

        $response = json_decode($exceptionEvent->getResponse()->getContent(), true);

        $this->assertEquals($exceptionCode, $response['status']);
        $this->assertEquals($exceptionMessage, $response['message']);
    }

    /**
     * @test
     */
    public function it_return_500_error_code_if_code_was_0()
    {
        $exceptionMessage = md5((string) microtime(true));
        $exceptionCode = 0;

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $exceptionEvent = new ExceptionEvent(
            $kernelMock,
            new Request(),
            HttpKernelInterface::MAIN_REQUEST,
            new Exception($exceptionMessage, $exceptionCode)
        );

        $kernelException = new ResponseSubscriber();
        $kernelException->onKernelException($exceptionEvent);

        $response = json_decode($exceptionEvent->getResponse()->getContent(), true);

        $this->assertEquals(500, $response['status']);
        $this->assertEquals($exceptionMessage, $response['message']);
    }

    /**
     * @test
     */
    public function it_return_validation_messages()
    {
        $field = md5((string) microtime(true));
        $error = md5((string) microtime(true));

        $violation = new ConstraintViolation(
            message: $error,
            messageTemplate: null,
            parameters: [],
            root: '',
            propertyPath: $field,
            invalidValue: '',
        );
        $constraingValidationList = new ConstraintViolationList([$violation]);

        $kernelMock = $this->createMock(HttpKernelInterface::class);
        $exceptionEvent = new ExceptionEvent(
            $kernelMock,
            new Request(),
            HttpKernelInterface::MAIN_REQUEST,
            ApiValidationException::withDetails($constraingValidationList)
        );

        $kernelException = new ResponseSubscriber();
        $kernelException->onKernelException($exceptionEvent);

        $response = json_decode($exceptionEvent->getResponse()->getContent(), true);

        $this->assertEquals(422, $response['status']);
        $this->assertEquals(RequestValidationException::MESSAGE, $response['message']);
    }

    /**
     * @test
     */
    public function it_return_subscribed_events()
    {
        $this->assertIsArray(ResponseSubscriber::getSubscribedEvents());
    }
}
