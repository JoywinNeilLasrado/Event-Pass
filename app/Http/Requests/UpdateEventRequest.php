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
            'available_tickets' => 'required|integer|min:0',
            'category_id'       => 'required|exists:categories,id',
            'tags'              => 'nullable|array',
            'tags.*'            => 'exists:tags,id',
            'poster_image'      => 'nullable|image|mimes:jpg,jpeg,png,webp|max:2048',
        ];
    }
}
