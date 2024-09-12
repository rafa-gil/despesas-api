<?php

namespace App\Http\Controllers\v1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Expense\{ExpenseStoreRequest, ExpenseUpdateRequest};
use App\Http\Resources\Expense\ExpenseResource;
use App\Models\Expense;
use App\Notifications\ExpenseNotification;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

class ExpenseController extends Controller
{
    use AuthorizesRequests;

    public function index(): AnonymousResourceCollection
    {
        $expenses = Expense::query()
            ->where('user_id', Auth::user()->id)
            ->orderBy('id', 'desc')
            ->get();

        return ExpenseResource::collection($expenses);
    }

    public function store(ExpenseStoreRequest $request): Response
    {
        $data = $request->validated();

        $data['user_id'] = Auth::user()->id;

        $expense = Expense::query()->create($data);

        Auth::user()->notify(new ExpenseNotification($expense->user_id, $expense->id));

        return response(new ExpenseResource($expense), ResponseAlias::HTTP_CREATED);
    }

    public function update(ExpenseUpdateRequest $request, Expense $expense): Response
    {
        $this->authorize('update', $expense);

        $data = $request->validated();

        $expense->query()->where('id', $expense->id)->update($data);

        $expense->refresh();

        return response(new ExpenseResource($expense));
    }

    public function destroy(Expense $expense): Response
    {
        $expense->delete();

        return response()->noContent();
    }
}
