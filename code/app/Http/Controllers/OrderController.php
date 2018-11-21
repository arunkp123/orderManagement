<?php
    
    namespace App\Http\Controllers;

    use App\Helpers\Helper;
    use App\Helpers\Messages;
    use App\Helpers\Validator;
    use App\Http\Models\Distance;
    use App\Http\Models\Order;
    use Illuminate\Http\Request;
    use Illuminate\Support\Facades\DB;
    use Mockery\Exception;

    class OrderController extends Controller
    {
    
        const ASSIGNED_ORDER_STATUS = 'ASSIGNED';
        const UNASSIGNED_ORDER_STATUS = 'UNASSIGNED';
        const ID = 'id';
        const DISTANCE = 'distance';
        const STATUS = 'status';
        
        const HTTP_STATUS_CODE_200 = 200;
        const HTTP_STATUS_CODE_204 = 204;
        const HTTP_STATUS_CODE_206 = 206;
        const HTTP_STATUS_CODE_400 = 400;
        const HTTP_STATUS_CODE_406 = 406;
        const HTTP_STATUS_CODE_409 = 409;
        const HTTP_STATUS_CODE_500 = 500;
        
        /**
         * @var \App\Http\Models\Order
         */
    
        protected $orderObj;
        /**
         * @var \App\Http\Models\Distance
         */
        protected $distanceObj;
    
        /**
         * @var \App\Helpers\Messages
         */
        protected $messages;
    
        /**
         * @var \App\Helpers\Helper
         */
        protected $helper;
    
        /**
         * @var \App\Helpers\Validator
         */
        protected $validator;
    
        /**
         * OrderController constructor.
         * @param \App\Http\Models\Order $orderObj
         * @param \App\Http\Models\Distance $distanceObj
         * @param \App\Helpers\Messages $messages
         */
        public function __construct(
            Order $orderObj,
            Distance $distanceObj,
            Messages $messages,
            Helper $helper,
            Validator $validator
        ) {
            $this->orderObj = $orderObj;
            $this->distanceObj = $distanceObj;
            $this->messages = $messages;
            $this->helper = $helper;
            $this->validator = $validator;
        }
    
        /**
         * Create new order entry
         *
         * @param \Illuminate\Http\Request $request
         * @return bool|\Illuminate\Http\JsonResponse|int|string
         */
        public function createOrder(Request $request)
        {
        
            try {
                if ( !isset($request->origin) || !isset($request->destination) || empty($request->origin) || empty($request->destination) || count($request->origin) <> 2 || count($request->destination) <> 2 ) {
                    return response()->json([
                        'error' => $this->messages->getMessages('INVALID_PARAMETERS'),
                    ], 206);
                }
            
                $startLatitude = $request->origin[0];
                $startLongitude = $request->origin[1];
                $endLatitude = $request->destination[0];
                $endLongitude = $request->destination[1];
            
                $validatorResponse = $this->validator->validateInputParameters($startLatitude, $startLongitude,
                    $endLatitude, $endLongitude);
            
                if ( 'failed' === $validatorResponse['status'] ) {
                    return response()->json([
                        'error' => $this->messages->getMessages($validatorResponse['error']),
                    ], self::HTTP_STATUS_CODE_206);
                }
            
                $distance = $this->helper->calculateDistance($startLatitude, $startLongitude, $endLatitude,
                    $endLongitude);
            
                if ( !is_int($distance['total_distance']) ) {
                    $response = array('error' => $distance['total_distance']);
                    return response()->json($response, self::HTTP_STATUS_CODE_400);
                }
            
            
                //Create new record
                $orderObj = $this->orderObj;
                $orderObj->status = self::UNASSIGNED_ORDER_STATUS;
                $orderObj->distance_id = $distance['distance_id'];
                $orderObj->save();
            
            
                return response()->json(array(
                    self::ID => $orderObj->id,
                    self::DISTANCE => $distance['total_distance'],
                    self::STATUS => $orderObj->status
                ), self::HTTP_STATUS_CODE_200);
            
            
            } catch (Exception $e) {
                $response = array('error' => $e->getMessage());
                return response()->json($response, self::HTTP_STATUS_CODE_500);
            }
        
        }
    
    
        /**
         * Update order
         *
         * @param \Illuminate\Http\Request $request
         * @param                          $id
         * @return \Illuminate\Http\JsonResponse
         */
        public function updateOrder(Request $request, $id)
        {
        
            try {
                if ( !isset($request->status) || 'TAKEN' !== $request->status ) {
                    return response()->json(['error' => $this->messages->getMessages('status_is_invalid')],self::HTTP_STATUS_CODE_206);
                }
                $this->orderObj = Order::findOrFail($id);
                $orderObj = $this->orderObj;
                if ( $orderObj->status == self::UNASSIGNED_ORDER_STATUS ) {
                    $orderObj->exists = true;
                    $orderObj->id = $id; //already exists in database.
                    $orderObj->status = self::ASSIGNED_ORDER_STATUS;
                    $orderObj->save();
                } else {
                    $response = array("error" => "Order already Taken");
                    return response()->json($response, self::HTTP_STATUS_CODE_409);
                }
            
                $response = array("status" => "SUCCESS");
                return response()->json($response, self::HTTP_STATUS_CODE_200);
            } catch (\Exception $e) {
                $response = array('error' => "Order ID not found");
                return response()->json($response, self::HTTP_STATUS_CODE_206);
            }
        }
    
        /**
         * @param \Illuminate\Http\Request $request
         * @return \Illuminate\Http\JsonResponse
         */
        public function getOrders(Request $request)
        {
            try {
                $queryString = $request->getQueryString();
            
                $getQueryParams = explode('&', $queryString);
                $page = 0;
                $limit = 0;
                if ( isset($getQueryParams[1]) ) {
                    $page = explode('=', $getQueryParams[1]);
                    $page = $page[1];
                }
                if ( isset($getQueryParams[0]) ) {
                    $limit = explode('=', $getQueryParams[0]);
                    $limit = $limit[1];
                }
            
                $validation = $this->helper->pageLimitCheck($limit, $page);
              
                if ( !empty($validation) ) {
                    $response = array('error' => $validation['error']);
                    return response()->json($response, self::HTTP_STATUS_CODE_206);
                }
                $records = DB::table('orders')->skip($page)->take($limit)->get();
            
                if ( !empty($records) ) {
                    $orders = [];
                
                    foreach ($records as $record) {
                    
                        $distance = $this->helper->getDistanceById($record->distance_id);
                    
                        $item = array(
                            self::ID => $record->id,
                            self::DISTANCE => $distance,
                            self::STATUS => $record->status
                        );
                    
                        array_push($orders, $item);
                    }
                
                    return response()->json($orders, self::HTTP_STATUS_CODE_200);
                
                } else {
                    $response = array('error' => "No Content Found");
                    return response()->json($response, self::HTTP_STATUS_CODE_204);
                
                }
            } catch (Exception $exception) {
                return response()->json($exception->getMessage(), self::HTTP_STATUS_CODE_500);
            }
        
        }
    }
