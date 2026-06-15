<?php

namespace App\Http\Resources\Api\V1;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LaporanBalasanResource extends JsonResource
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
            'pesan' => $this->pesan,
            'admin_name' => $this->user ? $this->user->name : 'Admin',
            'created_at' => $this->created_at->toISOString(),
            'time_ago' => $this->created_at->diffForHumans(),
        ];
    }
}
