<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id'                => $this->id,
            'title'             => $this->title,
            'description'       => $this->description,
            'date'              => $this->date->format('Y-m-d'),
            'time'              => $this->time,
            'location'          => $this->location,
            'available_tickets' => $this->available_tickets,
            'poster_image'      => $this->poster_image
                ? asset('storage/' . $this->poster_image)
                : null,
            'category'          => [
                'id'   => $this->category->id,
                'name' => $this->category->name,
            ],
            'tags'              => $this->tags->map(fn($tag) => [
                'id'   => $tag->id,
                'name' => $tag->name,
            ]),
            'owner'             => [
                'id'   => $this->user->id,
                'name' => $this->user->name,
            ],
            'created_at'        => $this->created_at->toISOString(),
        ];
    }
}
