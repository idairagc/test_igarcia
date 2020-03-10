<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;

class LoginController extends Controller
{
    /**
     * Login api
     *
     * @return \Illuminate\Http\Response
     */
    //http://127.0.0.1:8000/api/login?email=igc@gmail.com&password=12345678
    //http://127.0.0.1:8000/api/login?email=igc@gmail.com&password=qwertyu
    public function login(Request $request)
    {
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $user = Auth::user(); 
            $success['token'] =  $user->createToken('MyApp')->accessToken;  
            $success['name'] =  $user->name;
            
            $response = [
	            'success' => true,
	            'data'    => $success,
	            'message' => 'User login successfully.',
	        ];
   
            return response()->json($response, 200);
        } 
        else{ 
            return response()->json(['error'=>'Unauthorised'], 401);
        } 
    }
}