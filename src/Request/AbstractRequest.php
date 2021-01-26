<?php
declare(strict_types=1);

namespace MKoprek\RequestValidation\Request;

use Symfony\Component\HttpFoundation\Request;

abstract class AbstractRequest implements RequestInterface
{
    protected array $headers = [];
    protected array $params = [];

    public function getValidationData(): array
    {
        return $this->params;
    }

    public function populate(Request $request): void
    {
        $this->headers = $request->headers->all();

        $data = $request->query->all();

        if ($request->getMethod() !== 'GET') {
            $data = $request->request->all();
        }

        foreach ($data as $key => $val) {

            $this->params[$key] = $val;

            if (property_exists($this, $this->convertToCamelCase($key))) {
                $this->{$this->convertToCamelCase($key)} = $val;
            }
        }

        $routeParams = $request->attributes->all();

        if (array_key_exists('_route_params', $routeParams)) {
            foreach ($routeParams['_route_params'] as $key => $val) {

                $this->params[$this->convertToSnakeCase($key)] = $val;

                if (property_exists($this, $this->convertToCamelCase($key))) {
                    $this->{$this->convertToCamelCase($key)} = $val;
                }
            }
        }
    }

    private function convertToCamelCase(string $string)
    {
        return lcfirst(str_replace('_', '', ucwords($string, '_')));
    }

    private function convertToSnakeCase(string $string)
    {
        return strtolower(preg_replace('/(?<!^)[A-Z]/', '_$0', $string));
    }
}
