<?php

namespace App\Http\Middleware;
 
use Closure;
use App\Helpers\Messages;

class CheckYear
 
{
 
   /**
    * Handle an incoming request.
    *
    * @param  \Illuminate\Http\Request  $request
    * @param  \Closure  $next
    * @return mixed
    */
 
   protected $messages;
   
   
   public function __construct (Messages $messages)
   {
       $this->messages = $messages;
   }
    
    public function handle($request, Closure $next)
   {
        try {
            if ( !isset($request->origin) || !isset($request->destination) || empty($request->origin) || empty($request->destination) || count($request->origin) <> 2 || count($request->destination) <> 2 ) {
                return response()->json([
                    'error' => $this->messages->getMessages('INVALID_PARAMETERS'),
                ], 406);
            }
        } catch (Exception $e) {
            $response = array('error' => $e->getMessage());
            return response()->json($response, 200);
        }
       return $next($request); 
   }
}
