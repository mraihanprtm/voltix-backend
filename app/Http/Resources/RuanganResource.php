<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class RuanganResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // Use the snake_case name of the database column to access the data
        return [
            'id'              => $this->id,
            'user_id'         => $this->user_id,
            'nama_ruangan'    => $this->nama_ruangan,    // CORRECT: Use snake_case
            'panjang_ruangan' => (float) $this->panjang_ruangan, // CORRECT: Use snake_case
            'lebar_ruangan'   => (float) $this->lebar_ruangan,   // CORRECT: Use snake_case
            'jenis_ruangan'   => $this->jenis_ruangan,   // CORRECT: Use snake_case
            'created_at'      => $this->created_at,
            'updated_at'      => $this->updated_at,
            'uuid'            => $this->uuid,
            'deleted_at'      => $this->deleted_at,
            'isDeleted'       => (bool) $this->deleted_at, // A common way to represent isDeleted
            'lastModified'    => $this->lastModified,
        ];
    }
}
