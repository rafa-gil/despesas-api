<?php

namespace App\Policies;

use App\Models\{Expense, User};
use Illuminate\Support\Facades\Auth;

class ExpensePolicy
{
    public function view(User $user, Expense $expense): bool
    {
        return $user->is($expense->user) ? true : abort(403);
    }

    public function create(User $user): bool
    {
        return $user->is(Auth::user()) ? true : abort(403);
    }

    public function update(User $user, Expense $expense): bool
    {
        return $user->id === $expense->user_id;
    }

    public function delete(User $user, Expense $expense): bool
    {
        return $user->is($expense->user) ? true : abort(403);
    }
}
