<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\ResponseController as ResponseController;
use Illuminate\Http\Request;
use App\User;
use Illuminate\Support\Facades\Auth;
use Validator;
use Spatie\Permission\Traits\HasRoles;
use Spatie\Permission\Models\Role;
use App\Traits\UserValidationRulesTrait;

//Clase para la gestion de resgistros de usuarios
class RegisterController extends ResponseController
{
    use HasRoles, UserValidationRulesTrait;
    //Esto es un truquito para registrar al primer usuario que seria el Admin
    //crea también los roles
    public function register(Request $request)
    {
        //La tabla users tiene que estar vacía para poder entrar aquí
        if(is_null(User::all())){
        	//validaciones
            $validator = Validator::make($request->all(),$this->StoreValidationRules());
       		//si no cumple las validaciones responde con un error
            if($validator->fails()){
                return $this->sendError('Validation Error.', $validator->errors(),422);       
            }
       		
            //creamos los roles
            $role = Role::create(['name' => 'Admin']);
            $role = Role::create(['name' => 'Employee']);

       		//si todo es correcto crea el usuario con el role Admin
            $input = $request->all();
            $input['password'] = bcrypt($input['password']); 
            $user = User::create($input);
            $user->assignRole('Admin');
            $success['token'] =  $user->createToken('MyApp')->accessToken; 
            $success['name'] =  $user->name;

            //Devuelve el token de validacion, junto con un mensaje de éxito.
            return $this->sendResponse($success, 'User register successfully.');
        }
        return $this->sendError('Unauthorised.', ['error'=>'Unauthorised'],401);
    }
}