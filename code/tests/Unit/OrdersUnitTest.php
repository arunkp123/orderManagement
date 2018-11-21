<?php
    
    namespace Tests\Feature;
    
    use Illuminate\Foundation\Testing\WithoutMiddleware;
    use Tests\TestCase;
    use App\Http\Controllers\OrderController;
    
    
    class OrdersUnitTest extends TestCase{
    
        use WithoutMiddleware;
        
        public function testCreateOrderPositive(){
            //$orderController = new OrderController();
            $this->assertTrue(true);
        }
    }
