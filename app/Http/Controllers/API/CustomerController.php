<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\ResponseController as ResponseController;
use Illuminate\Http\Request;
use App\Customer;
use App\User; 
use Validator;
use App\Http\Resources\Customer as CustomerResource;

class CustomerController extends ResponseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customers = Customer::all();
        if(is_null($customers)){
            return $this->sendError('No customers.');
        }
        else{
            return $this->sendResponse(CustomerResource::collection($customers), 'Customers list successfully.');
        }
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $this->user = \Auth::user();
        $input = $request->all();
   
        $validator = Validator::make($input, [
            'name'      => 'required',
            'surname'   => 'required',
            'email'     => 'required|email|unique:customers'
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors());       
        }
        $input['user_id_create'] = $this->user->id;
        $input['user_id_update'] = $this->user->id;
        $customer = Customer::create($input);
   
        return $this->sendResponse(new CustomerResource($customer), 'Customer created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $customer = Customer::find($id);
  
        if (is_null($customer)) {
            return $this->sendError('Customer not found.');
        }
   
        return $this->sendResponse(new CustomerResource($customer), 'Customer retrieved successfully.');
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $this->user = \Auth::user();
        $customer = Customer::find($id);
        if (is_null($customer)) {
            return $this->sendError('Customer not found.');
        }
        
        $input = $request->all();
   
        $validator = Validator::make($request->all(), [
            'name'       => 'required',
            'surname'    => 'required',
            'email'      => 'sometimes|required|email|unique:customers'
        ]);
   
        if($validator->fails()){
            return $this->sendError('Validation Error.', $validator->errors(),422);        
        }
   
        $customer->name       = $input['name'];
        $customer->surname    = $input['surname'];
        $customer->email      = $input['email'];
        $customer->user_id_update = $this->user->id;
        $customer->update();
   
        return $this->sendResponse(new CustomerResource($customer), 'Customer updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $customer = Customer::find($id);
        if (is_null($customer)) {
            return $this->sendError('User not found.');
        }
        $customer->delete();
   
        return $this->sendResponse([], 'Customer deleted successfully.');
    }
}
