<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class TranslationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'key' => 'required|string|max:255',
            'value' => 'required|string',
            'language_id' => 'required|exists:languages,id',
            'tags' => 'array',
            'tags.*' => 'string|max:50'
        ];
    }
} 