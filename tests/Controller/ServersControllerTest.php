<?php

namespace App\Tests\Controller;

use App\Tests\Fixtures\DataFixtureTestCase;

/**
 * Class ServersControllerTest
 * @package App\Tests\Controller
 */
class ServersControllerTest extends DataFixtureTestCase
{

    public function setUp()
    {
        parent::setUp();
    }

    public function testGetServersResponseBody()
    {
        $response = $this->request('/api/servers', 'GET', []);
        $this->assertEquals(200, $response->getStatusCode());
        $this->assertEquals(2, count($this->responseBody()));
        $this->assertArrayHasKey('id', $this->responseBody()[0]);
        $this->assertArrayHasKey('name', $this->responseBody()[0]);
        $this->assertArrayHasKey('created_at', $this->responseBody()[0]);
        $this->assertArrayHasKey('rams_count', $this->responseBody()[0]);
    }

    public function testCreateServersuccessfully()
    {
        $data = [
            "name" => "DELL", 
            "price" => 12,
            "brand" => "RCA",
            "asset_id" => 122122,
            "rams" => [["type" => "DDR3", "size" => 2]]
        ];
        $params = json_encode($data);
        $response = $this->request('/api/servers', 'POST', [], $params);
        $this->assertEquals(201, $response->getStatusCode());
    }
    
    public function testCreateServerWithNoRam()
    {
        $data = [
            "name" => "DELL", 
            "price" => 12,
            "brand" => "RCA",
            "asset_id" => 122122,
        ];
        $params = json_encode($data);
        $response = $this->request('/api/servers', 'POST', [], $params);
        $this->assertEquals(400, $response->getStatusCode());
    }

    public function testCreateServerWithValidationError()
    {
        $response = $this->request('/api/servers', 'POST', []);
        $this->assertEquals(400, $response->getStatusCode());
    }


    public function testGetServersuccessfully()
    {
        $response = $this->request('/api/servers/1', 'GET', []);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testGetServerNotFound()
    {
        $response = $this->request('/api/servers/100', 'GET', []);
        $this->assertEquals(404, $response->getStatusCode());
    }

    public function testDeleteServersuccessfully()
    {
        $response = $this->request('/api/servers/1', 'DELETE', []);
        $this->assertEquals(200, $response->getStatusCode());
    }

    public function testDeleteServerErrorNotFound()
    {
        $response = $this->request('/api/servers/100', 'DELETE', []);
        $this->assertEquals(404, $response->getStatusCode());
    }
}