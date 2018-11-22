<?php

namespace App\Test\Feature\ApiController;

use Guzzle\Http\Exception\RequestException;
use Mockery\Exception;

class OrderControllerTest extends \PHPUnit\Framework\TestCase
{
    protected $client;

    protected function guzzleObject($base_uri, $header)
    {
        return $this->client = new \GuzzleHttp\Client([
            'base_uri' => $base_uri,
            'headers' => $header,
        ]);
    }

    protected function setUp()
    {
        $theHeaders = [
            'Content-Type' => 'application/json',
            'Accept' => 'application/json',
        ];

        $this->guzzleObject('http://nginx', $theHeaders);
    }

    public function testOrderCreationFailureIntegrations()
    {
        echo "\n \n ---------Starts Executing API Integration Test--------- \n \n";
    
        echo "\n \n ---------Creating orders with empty source co-ordinates should fail--------- \n \n";
    
        $response = $this->client->post('/orders', [
            'json' => [
                'origin' => ["", ""],
                'destination' => [
                    '28.535517',
                    '77.391029',
                ],
            ],
        ]);
    
        $this->assertEquals(206, $response->getStatusCode());
    }
    
    public function testOrderCreationSuccessIntegrations()
    {
        echo "\n \n ---------Creating orders with valid source & destination co-ordinates should work--------- \n \n";
    
        $response = $this->client->post('/orders', [
            'json' => [
                'origin' => [
                    '28.704060',
                    '77.102493',
                ],
                'destination' => [
                    '28.535517',
                    '77.391029',
                ],
            ],
        ]);
    
        $this->assertEquals(200, $response->getStatusCode());
    
        $data = json_decode($response->getBody()->getContents(), true);
    
        $this->assertArrayHasKey('id', $data);
        $this->assertArrayHasKey('status', $data);
        $this->assertArrayHasKey('distance', $data);
    }
    
    public function testOrderUpdateSuccessIntegration()
    {
        $response = $this->client->post('/orders', [
            'json' => [
                'origin' => [
                    '28.704060',
                    '77.102493',
                ],
                'destination' => [
                    '28.535517',
                    '77.391029',
                ],
            ],
        ]);
    
        $data = json_decode($response->getBody()->getContents(), true);
        
        echo "\n \n ---------Updating order created previously should pass--------- \n \n";
    
        $response = $this->client->patch('/orders/' . $data['id'], [
            'json' => [
                'status' => 'TAKEN',
            ],
        ]);
    
        $data = json_decode($response->getBody()->getContents(), true);
    
        $this->assertEquals(200, $response->getStatusCode());
    }
    public function testOrderUpdateFailureIntegration()
    {
        echo "\n \n ---------Updating order with Invalid Order ID should fail--------- \n \n";
    
        $response = $this->client->patch('/orders/10000', [
            'json' => [
                'status' => 'TAKEN',
            ],
        ]);
    
        $data = json_decode($response->getBody()->getContents(), true);
    
        $this->assertEquals(206, $response->getStatusCode());
    
    }
    
    public function testFetchOrderFailureIntegration()
    {
        echo "\n \n ---------Fetching orders with invalid page & limit should fail--------- \n \n";
    
        try {
            $response = $this->client->get('/orders', [
                'query' => [
                    'page' => 'A',
                    'limit' => 'B',
                ],
            ]);
    
    
            $this->assertEquals('206',$response->getStatusCode());
    
        } catch (RequestException $exception) {
            
            $statusCode = $exception->getCode();
            
            $this->assertEquals("206", $statusCode);
            //$this->assertNotNull($exception);
        }
    }
    
    public function testFetchOrderSuccessIntegration(){
        echo "\n \n ---------Fetching orders should return 1 order and pass--------- \n \n";
    
        $response = $this->client->get('/orders', [
            'query' => [
                'page' => 1,
                'limit' => 5,
            ],
        ]);
    
        $this->assertEquals(200, $response->getStatusCode());
    
        $data = json_decode($response->getBody()->getContents(), true);
    
        foreach ($data as $order) {
            $this->assertArrayHasKey('id', $order);
            $this->assertArrayHasKey('distance', $order);
        }
    
        echo "\n \n ---------API Integration Test Execution Finished ---------\n \n";
    
    
    }
    
    public function testUpdateOrderWithWrongParams(){
        echo "\n \n ---------Updating order with wrong request param name should fail--------- \n \n";
    
        $response = $this->client->patch('/orders/1', [
            'json' => [
                'status1' => 'TAKEN',
            ],
        ]);
    
        $data = json_decode($response->getBody()->getContents(), true);
    
        $this->assertEquals(206, $response->getStatusCode());
    }
    
    public function testCreateOrderWithInvalidSourceLocationCoordinates(){
        echo "\n \n ---------Creating orders with invalid source co-ordinates will throw a 206--------- \n \n";
    
        $response = $this->client->post('/orders', [
            'json' => [
                'origin' => [
                    'a',
                    'a',
                ],
                'destination' => [
                    '28.535517',
                    '77.391029',
                ],
            ],
        ]);
    
        $this->assertEquals(206, $response->getStatusCode());
    
    }
    
    public function testCreateOrderWithInvalidTargetLocationCoordinates(){
        echo "\n \n ---------Creating orders with invalid destination co-ordinates will throw a 206--------- \n \n";
        
        $response = $this->client->post('/orders', [
            'json' => [
                'origin' => [
                    '10.10',
                    '20.14',
                ],
                'destination' => [
                    'a',
                    '77.391029',
                ],
            ],
        ]);
        
        $this->assertEquals(206, $response->getStatusCode());
        
    }
    
    public function testFetchOrderFailureForNegativeValuesIntegration()
    {
        echo "\n \n ---------Fetching orders with negative values for page & limit should fail--------- \n \n";
        
        try {
            $response = $this->client->get('/orders', [
                'query' => [
                    'page' => '-1',
                    'limit' => '-1',
                ],
            ]);
    
            $this->assertEquals('206',$response->getStatusCode());
            
        } catch (RequestException $exception) {
            
            $statusCode = $exception->getCode();
            echo $statusCode;
            $this->assertEquals("206", $statusCode);
            //$this->assertNotNull($exception);
        }
    }
    
}
