<?php
    
    use Illuminate\Foundation\Testing\WithoutMiddleware;
    class OrderUnitTest extends Tests\TestCase{
    
        use WithoutMiddleware;
        
        function testGetOrder(){
            $stub = $this->createMock(\App\Helpers\helper::class);
            $stub->method('getDistanceById')->with(1)->willReturn(100);
            $this->assertEquals(100, $stub->getDistanceById(1));
        }
        
        function testPageLimitCheck(){
            $stub = $this->createMock(\App\Helpers\helper::class);
            $information = ['error' => 'INVALID_PARAMETERS'];
            $stub->method('pageLimitCheck')->with(-1,-1)->willReturn($information);
            $this->assertEquals($information, $stub->pageLimitCheck(-1,-1));
        }
    
        public function testStoreInvalidLonitudeRange()
        {
            
            $response = $this->json('POST', '/orders', [
                "origin" => [
                    "28.704060",
                    "77.102493"
                ],
                "destination" => [
                    "28.535517",
                    "197.391029"
                ]
            ]);
        
            $response->assertStatus(206);
        
            echo "\n \n Create Order Invalid longitude range test case passed \n \n";
        }
    
        public function testUpdateOrderAlreadyTaken()
        {
            $response = $this->json('PATCH', '/orders/1', [
                "status" => "TAKEN"
            ]);
        
            $response->assertStatus(206);
        
            echo "\n \n Update Order order already taken test case passed \n \n";
        
            echo "\n \n Unit Test Cases Execution Finished \n \n";
        }
    }
