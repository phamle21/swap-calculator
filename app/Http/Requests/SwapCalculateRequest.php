<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SwapCalculateRequest extends FormRequest
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
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'pair'             => ['required', 'string', 'max:20'],
            'lot_size'         => ['required', 'numeric', 'gt:0'],
            // Only require the swap rate that corresponds to the chosen position
            // If position_type == Long -> swap_long required; if Short -> swap_short required
            'swap_long'        => ['nullable', 'numeric', 'required_if:position_type,Long'],
            'swap_short'       => ['nullable', 'numeric', 'required_if:position_type,Short'],
            'position_type'    => ['required', 'in:Long,Short'],
            'days'             => ['required', 'integer', 'gt:0'],
            'cross_wednesday'  => ['nullable', 'boolean'],
            'profile_id'       => ['nullable', 'integer', 'exists:swap_profiles,id'],
        ];
    }

    protected function prepareForValidation(): void
    {
        $pair = strtoupper(trim((string)$this->input('pair')));
        $this->merge([
            'pair'            => $pair,
            'cross_wednesday' => filter_var($this->input('cross_wednesday', false), FILTER_VALIDATE_BOOLEAN),
        ]);
    }

    public function messages(): array
    {
        return [
            'pair.required' => 'Pair is required.',
            'lot_size.gt'   => 'Lot size must be greater than 0.',
            'days.gt'       => 'Days must be greater than 0.',
            'swap_long.required_if' => 'Swap Long rate is required when Position is Long.',
            'swap_short.required_if' => 'Swap Short rate is required when Position is Short.',
            'swap_long.numeric' => 'Swap Long must be a valid number.',
            'swap_short.numeric' => 'Swap Short must be a valid number.',
        ];
    }
}
