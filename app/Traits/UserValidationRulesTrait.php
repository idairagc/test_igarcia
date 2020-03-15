<?php
 
namespace App\Traits;

 
trait UserValidationRulesTrait {
 	
 	//Reglas de validación del metodo store del user controller
    public function StoreValidationRules() {

        return [
            'name'       => 'required|alpha|max:20',
            'surname'    => 'required|alpha|max:20',
            'email'      => 'required|email|unique:users|max:100',
            'password'   => 'required|min:8|max:20|alpha_num',
            'c_password' => 'required|same:password',
        ];
    }
    
    //Reglas de validación del metodo update del user controller
    public function UpdateValidationRules($id) {
        return [
            'name'       => 'required|alpha|max:20',
            'surname'    => 'required|alpha|max:20',
            'email'      => 'required|email|max:100|unique:users,email,'.$id,
        ];
    }

    //Reglas de validación del metodo changeRole del user controller
    public function ChangeRoleValidationRules() {
        return [
            'role' => 'required',
        ];
    }
 
}