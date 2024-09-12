<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Expense\ExpenseResource;
use App\Models\Expense;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function index(): AnonymousResourceCollection
    {
        $expenses = Expense::query()
        ->where('user_id', Auth::user()->id)
        ->orderBy('id', 'desc')
        ->get();

    return ExpenseResource::collection($expenses);
    }

    public function store(Request $request)
    {

    }

    public function show(string $id)
    {

    }

    public function update(Request $request, string $id)
    {

    }

    public function destroy(string $id)
    {

    }
}
