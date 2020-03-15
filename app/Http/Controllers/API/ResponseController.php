<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller as Controller;
use Illuminate\Http\Request;

//En este controlodar gestionamos las respuestas de las apis
class ResponseController extends Controller
{
    //Respuesta correcta
    public function sendResponse($result, $message)
    {
    	$response = [
            'success' => true,
            'data'    => $result,
            'message' => $message,
        ];
        //Siempre responde con codigo 200 OK
        return response()->json($response, 200); 
    }


    //Respuesta de error
    public function sendError($error, $errorMessages = [], $code = 404)
    {
    	$response = [
            'success' => false,
            'message' => $error,
        ];

        if(!empty($errorMessages)){
            $response['data'] = $errorMessages;
        }
        /*
        TIPOS DE ERROR  (No los utilizamos todos)
        201 Created 
        204 No Content 
        304 Not Modified 
        400 Bad Request 
        401 Unauthorized 
        403 Forbidden 
        404 Not Found 
        405 Method Not Allowed
        409 Conflict
        410 Gone
        415 Unsupported Media Type
        422 Unprocessable Entity 
        429 Too Many Requests
        */
        return response()->json($response, $code);
    }

    
}
