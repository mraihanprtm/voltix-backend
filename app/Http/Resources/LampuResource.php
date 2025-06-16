<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LampuResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        // 'whenLoaded' memastikan relasi 'perangkat' tidak dimuat kecuali diminta secara eksplisit,
        // yang dapat mencegah query N+1 yang tidak perlu.
        return [
            'id'             => $this->id,
            'uuid'           => $this->uuid,
            'perangkat_id'   => $this->perangkat_id,
            'jenis'          => $this->jenis,
            'lumen'          => $this->lumen,
            'isDeleted'      => $this->deleted_at !== null,
            'lastModified'   => $this->updated_at ? $this->updated_at->getTimestamp() * 1000 : null,
            'perangkat'      => new PerangkatResource($this->whenLoaded('perangkat')),
        ];
    }
}
