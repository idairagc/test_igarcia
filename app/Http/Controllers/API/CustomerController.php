<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\ResponseController as ResponseController;
use Illuminate\Http\Request;
use App\Customer;
use App\User; 
use Validator;
use App\Http\Resources\Customer as CustomerResource;
use App\Traits\ImageTrait;
use App\Traits\CustomerValidationRulesTrait;

//Controlador de los Customers
class CustomerController extends ResponseController
{
    use ImageTrait, CustomerValidationRulesTrait;
    /**
     * Lista a los Customers
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //Recuperamos todos los customers de bbdd
        $customers = Customer::all();
        //Si no hay customers enviamos un mensaje de error
        if(count($customers) == 0){
            return $this->sendError('No customers.');
        }
        //si no, la lista de customers junto con un mensaje de éxito.
        return $this->sendResponse(CustomerResource::collection($customers), 'Customers list successfully.');
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
     * Crea un nuevo Customer
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        //recuperamos el usuario que lo está dando de alta, para actualizar los campos:
        //user_id_created
        //user_id_updated
        $this->user = \Auth::user();

        //Recuperamos los datos que nos llegan del request
        $input = $request->all();
        //Validamos los campos
        $validator = Validator::make($input, $this->StoreValidationRules());
        //Si no cumple la validación, enviamos un mensaje de error
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        $input['user_id_created'] = $this->user->id;
        $input['user_id_updated'] = $this->user->id;
        //Guardamos la photo
        $input['photo'] =$this->uploadImage($request);
        //creamos el Customer
        $customer = Customer::create($input);

        //Enviamos los datos del customer junto a un mensaje de éxito.
        return $this->sendResponse(new CustomerResource($customer), 'Customer created successfully.');
    }

    /**
     * Muestra los datos de un Customer
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //Recuperamos el customer de bbdd
        $customer = Customer::find($id);
        //Si no existe enviamos un mensaje de error
        if (is_null($customer)) {
            return $this->sendError('Customer not found.');
        }
        //Si existe devolvemos los datos del customer junto a un mensaje de éxito.
        return $this->sendResponse(new CustomerResource($customer), 'Customer retrieved successfully.');
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
     * Modifica los datos de un Customer
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        //recuperamos el usuario que lo está dando de alta, para actualizar el campos user_id_updated
        $this->user = \Auth::user();
        //Recuperamos el customer de bbdd
        $customer = Customer::find($id);
        //Si no existe devolvemos un mensaje de error
        if (is_null($customer)) {
            return $this->sendError('Customer not found.');
        }
        //si existe, recuperamos los datos que nos llegan del request y validamos
        $input = $request->all();
        $validator = Validator::make($input, $this->UpdateValidationRules($customer->id));
        //Si no cumple la validación, devolvemos un mensaje de error
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(),422);        
        }
        //Si todo es correcto, actualizamos los datos
        $customer->name       = $input['name'];
        $customer->surname    = $input['surname'];
        $customer->email      = $input['email'];
        $customer->user_id_updated = $this->user->id;
        
        //Gestionamos la photo
        //Borramos la photo
        $customer->photo = $this->destroyImage($customer->photo);
        //Guardamos la photo
        $customer->photo = $this->uploadImage($request);
        
        //modificamos los datos del customer
        $customer->update();
        //Devolvemos los datos del customer junto a un mensaje de éxito.
        return $this->sendResponse(new CustomerResource($customer), 'Customer updated successfully.');
    }

    /**
     * Elimina un Customer
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //Buscamos el customer en bbdd
        $customer = Customer::find($id);
        //Si no existe devolvemos un error
        if (is_null($customer)) {
            return $this->sendError('Customer not found.');
        }
        //Borramos la photo
        $customer->photo = $this->destroyImage($customer->photo);
        
        //borramos el customer
        $customer->delete();
        
        //Enviamos un mensaje de éxito.
        return $this->sendResponse([], 'Customer deleted successfully.');
    }
}
