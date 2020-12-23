<?php
declare(strict_types=1);

namespace MKoprek\RequestValidation\Response;

use FutureNet\RestApi\Mapper\EntityMapper;
use JsonSerializable;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;

class ApiResponse extends JsonResponse
{
    public static function empty()
    {
        return new Response('', 204);
    }

    public static function collection(
        ?iterable $data,
        string $class,
        int $status = ApiResponse::HTTP_OK,
        array $headers = [],
        bool $json = false
    ): self {
        $return = [];

        if (is_iterable($data)) {
            /** @var JsonSerializable $item */
            foreach ($data as $item) {
                $return[] = (new $class($item))->jsonSerialize();
            }
        }

        return new self(
            [
                'data' => [
                    'collection' => $return,
                ],
            ],
            $status,
            $headers,
            $json
        );
    }

    public static function entity(
        $data,
        int $status = ApiResponse::HTTP_OK,
        array $headers = [],
        bool $json = false
    ): ApiResponse {
        return new self(
            $data instanceof JsonSerializable ? $data->jsonSerialize() : $data,
            $status,
            $headers,
            $json
        );
    }
}
