<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\ResponseController as ResponseController;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;

//Clase para el login de usuarios
class LoginController extends ResponseController
{
    //Api para el login de usuarios
    public function login(Request $request)
    {
    	//autentifico el usuario
        if(Auth::attempt(['email' => $request->email, 'password' => $request->password])){ 
            $user = Auth::user(); 
            $success['token'] =  $user->createToken('MyApp')->accessToken;  
            $success['name'] =  $user->name;
   			
   			//devuelve el token de autentificacion junto con un mensaje de éxito.
            return $this->sendResponse($success, 'User login successfully.');
        } 
        else{ //respuesta de error si no está autentificado
            return $this->sendError('Unauthorised.', ['error'=>'Unauthorised'],401);
        } 
    }
}