<?php

namespace App\Http\Requests\Post;

use Illuminate\Foundation\Http\FormRequest;

class DeletePostRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('delete', $this->route('post'));
    }

    public function rules(): array
    {
        return [];
    }
}
