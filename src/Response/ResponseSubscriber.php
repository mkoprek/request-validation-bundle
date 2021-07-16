<?php
declare(strict_types=1);

namespace MKoprek\RequestValidation\Response;

use MKoprek\RequestValidation\Request\Exception\RequestValidationException;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Event\ExceptionEvent;
use Symfony\Component\HttpKernel\Exception\HttpExceptionInterface;
use Symfony\Component\HttpKernel\KernelEvents;

class ResponseSubscriber implements EventSubscriberInterface
{
    public function onKernelException(ExceptionEvent $event): void
    {
        $exception = $event->getThrowable();

        $code = $exception instanceof HttpExceptionInterface ? $exception->getStatusCode() : $exception->getCode();

        if ($code === 0) {
            $code = Response::HTTP_INTERNAL_SERVER_ERROR;
        }

        $data = [
            'status' => $code,
            'message' => $exception->getMessage(),
        ];

        if ($exception instanceof RequestValidationException) {
            foreach ($exception->getErrors() as $error) {
                $data['details'][] = [
                    'field' => $error->getPropertyPath(),
                    'error' => $error->getMessage(),
                ];
            }
        }

        $response = new Response((string) json_encode($data), $code);
        $response->headers->set('Content-Type', 'application/problem+json');

        $event->setResponse($response);
    }

    /** @return array<array<mixed>> */
    public static function getSubscribedEvents(): array
    {
        return [
            KernelEvents::EXCEPTION => ['onKernelException', 1],
        ];
    }
}
