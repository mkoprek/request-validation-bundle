<?php
declare(strict_types=1);

namespace MKoprek\RequestValidation\Response;

use FutureNet\RestApi\Mapper\EntityMapper;
use JsonSerializable;
use Symfony\Component\HttpFoundation\JsonResponse;

class ApiResponse extends JsonResponse
{
    public static function empty(int $status = self::HTTP_OK): self
    {
        return new self(null, $status, [], false);
    }

    public static function collection(
        $data,
        int $status = ApiResponse::HTTP_OK,
        array $headers = [],
        bool $json = false
    ): self {
        return new self(
            [
                'data' => [
                    'collection' => $data instanceof JsonSerializable ? $data->jsonSerialize() : $data,
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
