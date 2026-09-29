<?php

declare(strict_types=1);

namespace App\Http\Requests;

use App\Models\Step;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Validation\Rule;

class UpdateIdeaRequest extends StoreIdeaRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return $this->user()->can('update', $this->route('idea'));
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            ...parent::rules(),
            'steps.*' => ['array:id,description'],
            'steps.*.id' => [
                'nullable',
                'integer',
                Rule::exists(Step::class, 'id')->where('idea_id', $this->route('idea')->id),
            ],
        ];
    }
}
