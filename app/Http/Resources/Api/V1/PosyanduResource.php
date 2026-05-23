<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PosyanduResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'nama_posyandu' => $this->nama_posyandu,
            'alamat' => $this->alamat,
            'latitude' => (float) $this->latitude,
            'longitude' => (float) $this->longitude,
            'status' => $this->status,
            'keterangan' => $this->keterangan,
            'desa' => new DesaResource($this->whenLoaded('desa')),
        ];
    }
}
