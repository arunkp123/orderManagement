<?php

namespace App\Helpers;

class Validator {

    /**
     * Validate Input Parameters.
     *
     * @params float $startLatitude
     * @params float $startLongitude
     * @params float $endLatitude
     * @params float $endLongitude
     *
     * @return array
     */
    public function validateInputParameters($startLatitude, $startLongitude, $endLatitude, $endLongitude)
    {
        $response = ['status' => 'success'];

        if ($startLatitude == $endLatitude && $startLongitude == $endLongitude) {
            return $response = ['status' => 'failed' , 'error'=> 'REQUESTED_ORIGIN_DESTINATION_SAME'];
        } elseif (!$startLatitude || !$startLongitude || !$endLatitude || !$endLongitude) {
            return $response = ['status' => 'failed' , 'error'=> 'REQUEST_PARAMETER_MISSING'];
        } elseif ($startLatitude < -90 || $startLatitude > 90 || $endLatitude < -90 || $endLatitude > 90) {
            return $response = ['status' => 'failed' , 'error'=> 'LATITUDE_OUT_OF_RANGE'];
        } elseif ($startLongitude< -180 || $startLongitude> 180 || $endLongitude< -180 || $endLongitude> 180) {
            return $response = ['status' => 'failed' , 'error'=> 'LONGITUDE_OUT_OF_RANGE'];
        }elseif(!is_numeric($startLatitude) || !is_numeric($endLatitude) || !is_numeric($startLongitude) || !is_numeric($endLongitude)){
            return $response = ['status' => 'failed' , 'error'=> 'INVALID_PARAMETERS'];
        }
        

        return $response;
    }
}
