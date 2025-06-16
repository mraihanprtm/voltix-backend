<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use App\Http\Resources\LampuResource;

class PerangkatResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'uuid' => $this->uuid,
            'user_id' => $this->user_id,
            'nama' => $this->nama,
            'jumlah' => $this->jumlah,
            'daya' => $this->daya,
            'jenis' => $this->jenis,
            'lampu_detail' => new LampuResource($this->whenLoaded('lampuDetail')),
            'lastModified' => $this->updated_at->getTimestamp() * 1000,
        ];
    }
}
