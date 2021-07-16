<?php
declare(strict_types=1);

namespace Tests\MKoprek\RequestValidation\Request;

use PHPUnit\Framework\TestCase;
use Symfony\Component\HttpFoundation\Request;

class AbstractRequestTest extends TestCase
{
    private function createRequest(
        string $method,
        array $attributes = [],
        array $post = [],
        array $query = []
    ): Request {
        $content = json_encode($post);
        $request = new Request($query, $post, $attributes, [], [], [], $content);
        $request->setMethod($method);

        return $request;
    }

    /**
     * @test
     */
    public function it_can_populate_get_variables(): void
    {
        $id = md5((string) microtime(true));
        $name = md5((string) microtime(true));
        $data = [
            'id' => $id,
            'name' => $name,
        ];
        $symfonyRequest = new Request($data);

        $request = new RequestStub();
        $request->populate($symfonyRequest);

        $this->assertEquals($id, $request->getId());
        $this->assertEquals($name, $request->getName());
    }

    /**
     * @test
     */
    public function it_can_populate_post_variables(): void
    {
        $id = md5((string) microtime(true));
        $name = md5((string) microtime(true));
        $data = json_encode([
            'id' => $id,
            'name' => $name,
        ]);
        $symfonyRequest = new Request(content: $data);
        $symfonyRequest->setMethod(Request::METHOD_POST);

        $request = new RequestStub();
        $request->populate($symfonyRequest);

        $this->assertEquals($id, $request->getId());
        $this->assertEquals($name, $request->getName());
    }

    /**
     * @test
     */
    public function it_can_populate_route_params()
    {
        $id = md5((string) microtime(true));
        $name = md5((string) microtime(true));
        $routeParams = [
            '_route_params' => [
                'id' => $id,
                'name' => $name,
            ]
        ];
        $symfonyRequest = new Request(attributes: $routeParams);

        $request = new RequestStub();
        $request->populate($symfonyRequest);

        $this->assertEquals($id, $request->getId());
        $this->assertEquals($name, $request->getName());
    }
}
