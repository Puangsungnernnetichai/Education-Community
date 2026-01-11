<?php

namespace App\Http\Requests;

use App\Models\Comment;
use Illuminate\Foundation\Http\FormRequest;

class UpdateCommentRequest extends FormRequest
{
    protected $errorBag = 'comment';

    public function authorize(): bool
    {
        $comment = $this->route('comment');
        if (! $comment instanceof Comment) {
            return false;
        }

        return $this->user()?->can('update', $comment) ?? false;
    }

    public function rules(): array
    {
        return [
            'body' => ['required', 'string', 'max:5000'],
            '_depth' => ['nullable', 'integer', 'min:0', 'max:12'],
        ];
    }
}
