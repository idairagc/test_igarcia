<?php
 
namespace App\Traits;

use Illuminate\Http\Request;
 
trait ImageTrait {
 	
 	//Metodo para upload la photo
    public function uploadImage(Request $request) {
        $file = $request->file('photo');
        //Si no hay photo devolvemos null
        if (empty($file)) {
        	return null;
        }
        //Creamos un nombre nuevo
        $name=time().$file->getClientOriginalName();
        //guardamos en /images
        $path= $file->move(public_path('images'),$name);
        //En bbdd, sólo guardamos el nombre de la foto, aunque devolveremos la url
        return $name;
    }

    //Metodo para borrar una photo
    public function destroyImage($image) {
        if (!empty($image)) {
            //Si tenía una foto, la borramos 
            unlink(public_path('images'). '\\' .$image);
        }
        return null;
    }
 
}