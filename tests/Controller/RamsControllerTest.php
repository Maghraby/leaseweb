<?php

namespace App\Tests\Controller;

use App\Tests\Fixtures\DataFixtureTestCase;

/**
 * Class RamsControllerTest
 * @package App\Tests\Controller
 */
class RamsControllerTest extends DataFixtureTestCase
{

    public function setUp()
    {
        parent::setUp();
    }

    public function testGetramsResponseBody()
    {
        $response = $this->request('/api/servers/1/rams', 'GET', []);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(2, count($this->responseBody()));
        $this->assertArrayHasKey('id', $this->responseBody()[0]);
        $this->assertArrayHasKey('type', $this->responseBody()[0]);
        $this->assertArrayHasKey('size', $this->responseBody()[0]);
        $this->assertArrayHasKey('created_at', $this->responseBody()[0]);
    }

    public function testCreateramsuccessfully()
    {
        $data = [
            "type" => "DDR3", 
            "size" => 12,
        ];
        $params = json_encode($data);
        $response = $this->request('/api/servers/1/rams', 'POST', [], $params);
        $this->assertEquals(201, $response->getStatusCode());
    }

    public function testCreateRamWithValidationError()
    {
        $response = $this->request('/api/servers/1/rams', 'POST', []);
        $this->assertEquals(400, $response->getStatusCode());
    }


    public function testGetramsuccessfully()
    {
        $response = $this->request('/api/servers/1/rams/1', 'GET', []);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetRamServerNotFound()
    {
        $response = $this->request('/api/servers/100/rams/100', 'GET', []);
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testGetRamNotFound()
    {
        $response = $this->request('/api/servers/1/rams/100', 'GET', []);
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testDeleteramsuccessfully()
    {
        $response = $this->request('/api/servers/1/rams/1', 'DELETE', []);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testDeleteRamCanNotDeleteOnlyOneRam()
    {
        $response = $this->request('/api/servers/2/rams/3', 'DELETE', []);
        $this->assertEquals(422, $response->getStatusCode());
    }
}