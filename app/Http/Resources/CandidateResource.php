<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CandidateResource extends JsonResource
{
    /**
     * Transform the resource collection into an array.
     *
     * @return array<int|string, mixed>
     */
    public function toArray(Request $request): array
    {
        // if the user uuid does not exist in the collection instance
        if (empty($this->uuid)) {
            return [];
        }

        return [
            'id'    => $this->uuid,
            'name'  => $this->full_name,
            'email' => $this->email,
        ];
    }
}
