<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\ResponseController as ResponseController;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;

//Clase para la gestion de resgistros de usuarios
class RegisterController extends ResponseController
{
    //Api para el registro de un usuario
    //creo que cuando meta los permisos esto desaparece...
    public function register(Request $request)
    {
    	//validaciones
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'surname' => 'required',
            'email' => 'required|email|unique:users',
            'password' => 'required',
            'c_password' => 'required|same:password',
        ]);
   		
   		//si no cumple las validaciones responde con un error
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(),422);       
        }
   		
   		//si todo es correcto crea el usuario
        $input = $request->all();
        $input['password'] = bcrypt($input['password']); 
        $user = User::create($input);
        $success['token'] =  $user->createToken('MyApp')->accessToken; 
        $success['name'] =  $user->name;

        //Responde con exito.
        return $this->sendResponse($success, 'User register successfully.');
    }
}