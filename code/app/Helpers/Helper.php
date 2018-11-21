<?php
    
    namespace app\Helpers;
    
    use App\Http\Models\Distance;
    use Illuminate\Support\Facades\DB;
    
    
    class helper
    {
        
        protected $distanceObj;
        protected $latLangInfoArray;
        protected $distanceMatrix;
        protected $messages;
        
        public function __construct (Distance $distanceObj, Messages $messages)
        {
            $this->distanceObj = $distanceObj;
            
            $this->messages = $messages;
        }
        
        public function calculateDistance ($startLatitude, $startLongitude, $endLatitude, $endLongitude)
        {
            
            $distanceData = DB::table('distances')->where([
                ['start_latitude', '=', $startLatitude],
                ['start_longitude', '=', $startLongitude],
                ['end_latitude', '=', $endLatitude],
                ['end_longitude', '=', $endLongitude],
            ])->get();
            
            
            //validating to get data from google api with existing records
            $distance_id = 0;
            if ( count($distanceData) > 0 ) {
                $totalDis = $distanceData[0]->distance;
                $distance_id = $distanceData[0]->id;
            } else {
                $origin = $startLatitude . "," . $startLongitude;
                $destination = $endLatitude . "," . $endLongitude;
                
                $totalDis = $this->getDistanceMatrix($origin, $destination);
    
                if( !is_int($totalDis)) {
                    return ['distance_id' => $distance_id, 'total_distance' => $totalDis];
                }
                
               // $totalDis = 88;
                //inserting data in distance table
               
                    $distance = new Distance;
                    $distance->start_latitude = $startLatitude;
                    $distance->start_longitude = $startLongitude;
                    $distance->end_latitude = $endLatitude;
                    $distance->end_longitude = $endLongitude;
                    $distance->distance = $totalDis;
                    $distance->save();
                
        
            }
            return ['distance_id' => $distance_id, 'total_distance' => $totalDis];
            
            
        }
        
        /**
         * Gets the distance from google api.
         *
         * @params string $origin
         * @params string destination
         *
         * @return int
         */
        public function getDistanceMatrix ($origin, $destination)
        {
            $googleApiKey = env('GOOGLE_API_KEY');
    
            $queryString = env('GOOGLE_API_URL')."?units=imperial&origins=" . $origin . "&destinations=" . $destination . "&key=" . $googleApiKey;
    
            
            $data = file_get_contents($queryString);
            
           $data = json_decode($data);
            
            
            if ( !$data || $data->status == 'REQUEST_DENIED' || $data->status == 'OVER_QUERY_LIMIT' || $data->status == 'NOT_FOUND' || $data->status == 'ZERO_RESULTS' ) {
                return (isset($data->status)) ? $data->status : 'GOOGLE_API_NULL_RESPONSE';
            }
           $distanceValue = (int)$data->rows[0]->elements[0]->distance->value;
            
            return $distanceValue;
        }
        
        /**
         * Return distance by distance id
         *
         * @param $id
         * @return mixed
         */
        public function getDistanceById ($id)
        {
            $distanceData = DB::table('distances')->where([
                ['id', '=', $id]
            ])->get();
            
            return $distanceData[0]->distance;
        }
    
        /**
         * @param $limit
         * @param $page
         * @return array
         */
        public function pageLimitCheck($limit, $page){
            $information = [];
            if (!isset($limit) || !isset($page)) {
                return $information = ['error' => 'REQUEST_PARAMETER_MISSING'];
            }
            elseif(!is_numeric($limit) || !is_numeric($page)) {
                return $information = ['error' => 'INVALID_PARAMETER_TYPE'];
            }
            elseif($limit  < 1 || $page  < 1) {
                return $information = ['error' => 'INVALID_PARAMETERS'];
            }else{
    
                return $information;
            }
        
        }
    }
