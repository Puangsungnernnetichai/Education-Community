<?php

namespace App\Http\Requests;

use App\Models\Post;
use Illuminate\Foundation\Http\FormRequest;

class UpdatePostRequest extends FormRequest
{
    protected $errorBag = 'post';

    public function authorize(): bool
    {
        $post = $this->route('post');
        if (! $post instanceof Post) {
            return false;
        }

        return $this->user()?->can('update', $post) ?? false;
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'body' => ['required', 'string', 'max:10000'],
            'is_private' => ['sometimes', 'boolean'],
            'tags' => ['nullable', 'array', 'max:10'],
            'tags.*' => ['string', 'max:50'],
            '_render' => ['nullable', 'in:index,feed'],
        ];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('tags') && is_string($this->input('tags'))) {
            $raw = (string) $this->input('tags');
            $parts = preg_split('/,/', $raw) ?: [];
            $tags = [];
            foreach ($parts as $part) {
                $part = trim((string) $part);
                $part = preg_replace('/\s+/', ' ', $part ?? '');
                $part = ltrim($part, "#");
                if (! $part) continue;
                $key = mb_strtolower($part);
                $tags[$key] = $part;
            }
            $this->merge(['tags' => array_values($tags)]);
        }

        if ($this->has('is_private')) {
            $this->merge([
                'is_private' => filter_var($this->input('is_private'), FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE) ?? false,
            ]);
        }
    }
}
