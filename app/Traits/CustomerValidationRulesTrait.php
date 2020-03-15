<?php
 
namespace App\Traits;

 
trait CustomerValidationRulesTrait {
 	
 	//Reglas de validación del metodo store del user controller
    public function StoreValidationRules() {

        return [
            'name'       => 'required|alpha|max:20',
            'surname'    => 'required|alpha|max:20',
            'email'      => 'required|email|unique:customers|max:100',
            'photo'      => 'sometimes|required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ];
    }
    
    //Reglas de validación del metodo update del user controller
    public function UpdateValidationRules($id) {
        return [
            'name'       => 'required|alpha|max:20',
            'surname'    => 'required|alpha|max:20',
            'email'      => 'required|email|max:100|unique:customers,email,'.$id,
            'photo'      => 'sometimes|required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ];
    }
 
}