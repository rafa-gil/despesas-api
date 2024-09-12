<?php

namespace App\Http\Requests\Expense;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class ExpenseUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('update', $this->route('expense'));
    }

    public function rules(): array
    {
        return [
            'description'     => 'string|min:5|max:191',
            'date'            => 'date|before_or_equal:today',
            'amount_in_cents' => 'integer|min:0',
        ];
    }
}
