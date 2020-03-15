<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

//Formato de respuesta para las APis de los customers
class Customer extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request)
    {
        if (!empty($this->photo)) {
            $this->photo=public_path('images') . '\\' . $this->photo;
        }
        
        return [
            'id' => $this->id,
            'name' => $this->name,
            'surname' => $this->surname,
            'email' => $this->email,
            'photo' => $this->photo,
            'created_at' => $this->created_at->format('d/m/Y'),
            'user_id_created' => $this->user_id_created,
            'updated_at' => $this->updated_at->format('d/m/Y'),
            'user_id_updated' => $this->user_id_updated,
        ];
    }
}
