<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Enums\IdeaStatus;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;
use Illuminate\Validation\Rules\File;

class StoreIdeaRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'max:255'],
            'description' => ['required', 'string', 'min:10'],
            'status' => ['required', Rule::enum(IdeaStatus::class)],
            'links' => ['array', 'max:10'],
            'links.*' => ['string', 'url:http,https', 'max:255'],
            'image' => ['nullable', File::image()->max(2 * 1024)],
            'steps' => ['array', 'max:20'],
            'steps.*' => ['array:id,description'],
            'steps.*.id' => ['prohibited'],
            'steps.*.description' => ['required', 'string', 'max:255'],
        ];
    }

    /**
     * Get custom attributes for validator errors.
     *
     * @return array<string, string>
     */
    public function attributes(): array
    {
        return [
            'links.*' => 'link',
            'steps.*.description' => 'step',
        ];
    }

    /**
     * Drop the blank link and step rows the form submits for empty inputs.
     */
    protected function prepareForValidation(): void
    {
        $this->merge([
            'links' => array_values(array_filter(
                (array) $this->input('links', []),
                filled(...),
            )),
            'steps' => array_values(array_filter(
                (array) $this->input('steps', []),
                fn (mixed $step): bool => is_array($step) && filled($step['description'] ?? null),
            )),
        ]);
    }
}
