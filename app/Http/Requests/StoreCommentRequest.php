<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreCommentRequest extends FormRequest
{
    protected $errorBag = 'comment';

    public function authorize(): bool
    {
        // Authorization is checked in the controller (post visibility + auth middleware).
        return (bool) $this->user();
    }

    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'max:5000'],
            'parent_id' => ['nullable', 'integer'],
            '_depth' => ['nullable', 'integer', 'min:0', 'max:12'],
        ];
    }
}
