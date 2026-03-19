<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEventRequest extends FormRequest
{
    public function authorize(): bool
    {
        return auth()->check();
    }

    public function rules(): array
    {
        return [
            'title'             => 'required|string|max:255',
            'description'       => 'required|string',
            'date'              => 'required|date',
            'time'              => 'required',
            'location'          => 'required|string|max:255',
            'tickets'           => 'required|array|min:1',
            'tickets.*.id'      => 'nullable|integer|exists:ticket_types,id',
            'tickets.*.name'    => 'required|string|max:255',
            'tickets.*.price'   => 'required|numeric|min:0',
            'tickets.*.capacity'=> 'required|integer|min:0',
            'tickets.*.description' => 'nullable|string|max:500',
            'category_id'       => 'required|exists:categories,id',
            'tags'              => 'nullable|array',
            'tags.*'            => 'exists:tags,id',
            'poster_image'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
            'images'            => 'nullable|array|max:10',
            'images.*'          => 'image|mimes:jpg,jpeg,png,webp|max:2048',
            'is_featured'       => 'nullable|boolean',
        ];
    }
}
