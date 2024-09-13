<?php

namespace App\Http\Requests\Expense;

use App\Models\Expense;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Gate;

class ExpenseStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return Gate::allows('create', Expense::class);
    }

    public function rules(): array
    {
        return [
            'description' => 'required|string|min:5|max:191',
            'date'        => 'required|before_or_equal:today',
            'amount'      => 'numeric|decimal:2|between:0.01,999999.99',
        ];
    }
}
