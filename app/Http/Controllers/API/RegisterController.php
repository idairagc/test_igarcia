<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;

class RegisterController extends Controller
{
    /**
     * Register api
     *
     * @return \Illuminate\Http\Response
     */
    //pruebo esto con: 
    //http://127.0.0.1:8000/api/register?name=idaira&email=igc@gmail.com&password=12345678&c_password=12345678
    //http://127.0.0.1:8000/api/register?name=juan&email=juan@gmail.com&password=qwertyu&c_password=qwertyu

    public function register(Request $request)
    {
    	//fallo: hay que revisar estas validaciones
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);
   
        if($validator->fails()){
            return response()->json(['error'=>$validator->errors()], 401);      
        }
   
        $input = $request->all();
        $input['password'] = bcrypt($input['password']); 
        $user = User::create($input);
        $success['token'] =  $user->createToken('MyApp')->accessToken; 
        $success['name'] =  $user->name;

        $response = [
            'success' => true,
            'data'    => $success,
            'message' => 'User register successfully.',
        ];
   
        return response()->json($response, 200);
    }
}