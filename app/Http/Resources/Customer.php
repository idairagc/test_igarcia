<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

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
        return [
            'id' => $this->id,
            'name' => $this->name,
            'surname' => $this->surname,
            'email' => $this->email,
            'photo' => $this->photo,
            'created_at' => $this->created_at->format('d/m/Y'),
            'user_id_create' => $this->user_id_create,
            'updated_at' => $this->updated_at->format('d/m/Y'),
            'user_id_update' => $this->user_id_update,
        ];
    }
}
