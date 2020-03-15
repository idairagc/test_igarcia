<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\ResponseController as ResponseController;
use Illuminate\Http\Request;
use App\User; 
use Illuminate\Support\Facades\Auth; 
use Validator;
use App\Http\Resources\User as UserResource;
use Spatie\Permission\Models\Role;
use App\Traits\UserValidationRulesTrait;

//Controlador de los Users
class UserController extends ResponseController
{
    use UserValidationRulesTrait;
    /**
     * Lista a los users
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Recuperamos todos los users de bbdd
        $users = User::all();
        
        //Como mínimo tiene que estar el admin registrado para poder ejecutar esta operación, no hace falta validar si hay usuarios.
        return $this->sendResponse(UserResource::collection($users), 'Users list successfully.');
    }

    /**
     * Esta no se usa en api
     *
     * @return \Illuminate\Http\Response
    */
    public function create()
    {
        //
    }

    /**
     * Crea un nuevo user
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //Recuperamos los datos que nos llegan del request
        $input = $request->all();
        //validamos los campos
        $validator = Validator::make($input,$this->StoreValidationRules());
        //si no cumple las validaciones responde con un error
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(),422);       
        }
        //encriptamos la contraseña
        $input['password'] = bcrypt($input['password']);
        //si todo es correcto crea el usuario y le asigna el role Employee 
        $user = User::create($input);
        $user->assignRole('Employee');

        //No voy a devolver el tokken del usuario, xq esto lo dará de alta el Admin y cuando el usuario se loguee, le mandará el tokken.
        //$success['token'] =  $user->createToken('MyApp')->accessToken; 
        //$success['name'] =  $user->name;

        //Devuelvo los datos del usuario registrado junto con un mensaje de éxito
        return $this->sendResponse(new UserResource($user), 'User register successfully.');
    }

    /**
     * Muestra los datos de un user
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //Recuperamos el user de bbdd
        $user = User::find($id);
        //Si no existe enviamos un mensaje de error
        if (is_null($user)) {
            return $this->sendError('User not found.');
        }
        //Si existe devolvemos los datos del user junto a un mensaje de éxito.
        return $this->sendResponse(new UserResource($user), 'User retrieved successfully.');
    }

    /**
     * Esta no se usa en api
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
    */
    public function edit($id)
    {
        //
    }
    

    /**
     * Modifica los datos de un user
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //Recuperamos el user de bbdd
        $user = User::find($id);
        //Si no existe devolvemos un mensaje de error
        if (is_null($user)) {
            return $this->sendError('User not found.');
        }
        //si existe, recuperamos los datos que nos llegan del request y validamos
        $input = $request->all();
        //validamos los campos
        $validator = Validator::make($input, $this->UpdateValidationRules($user->id));    
        //Si no cumple la validación, devolvemos un mensaje de error
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(),422);        
        }
        //Si todo es correcto, actualizamos los datos
        $user->name       = $input['name'];
        $user->surname    = $input['surname'];
        $user->email      = $input['email'];
        $user->update();
        //Devolvemos los datos del user junto a un mensaje de éxito.
        return $this->sendResponse(new UserResource($user), 'User updated successfully.');
    }

    /**
     * Elimina un user
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //Buscamos el user en bbdd
        $user = User::find($id);
        //Si no existe devolvemos un error
        if (is_null($user)) {
            return $this->sendError('User not found.');
        }
        //Esto es para que el admin no se elimine a él mismo
        $this->user = \Auth::user();
        if ($user->id == $this->user->id){
            return $this->sendError('Can not delete yourself.',[],403);
        }
        //Eliminamos al user
        $user->delete();
        //Si todo va bien devolvemos un mensaje de exito.
        return $this->sendResponse([], 'User deleted successfully.');
    }

    /**
     * Modifica el role del user (sólo tiene acceso el admin)
     *
     * @return \Illuminate\Http\Response
     */
    public function changeRole(Request $request, $id)
    {
        //Buscamos el user en bbdd
        $user = User::find($id);
        //Si no existe devolvemos un error
        if(is_null($user)){
            return $this->sendError('User not found.');
        }
        //si existe, recuperamos los datos que nos llegan del request y validamos
        $input = $request->all();
        $validator = Validator::make($input, $this->ChangeRoleValidationRules());
        //si no cumple las validaciones responde con un error
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(),422);       
        }
        //Buscamos el role en bbbd
        $role = Role::find($input['role']);
        //Si no existe devolvemos un error.
        if(is_null($role)){
            return $this->sendError('No exist that role.');
        }
        //si el user ya tiene asignado el role, devolvemos un mensaje de error
        if($user->hasRole($role->name)){
            return $this->sendError('User already has this role.',[],422);
        }
        //Borramos todos los posibles roles que tenga el user
        foreach ($user->getRoleNames() as $key => $value) {
            $user->removeRole($value);
        }
        //Le asignamos el nuevo role
        $user->assignRole($input['role']);
        //devolvemos un mensaje de éxito
        return $this->sendResponse([], 'Status change successfully.');
    }
}
