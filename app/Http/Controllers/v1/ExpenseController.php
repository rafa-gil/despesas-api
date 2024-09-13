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
use OpenApi\Annotations as OA;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

/**
   * @OA\OpenApi(
   *    @OA\Info(
   *       title="Despesas API",
   *       version="1.0.0",
   *       description="API para gerenciamento de despesas",
   *       @OA\Contact(
   *           email="suporte@exemplo.com"
   *       )
   *    ),
   *    @OA\Server(
   *       url="http://localhost/",
   *       description="Servidor local"
   *    )
   * )
   */
class ExpenseController extends Controller
{
    use AuthorizesRequests;

    /**
     * @OA\Get(
     *    path="/api/v1/expenses",
     *    security={{"bearerAuth": {}}},
     *    tags={"Expenses"},
     *    summary="Get all expenses",
     *    description="Get all expenses",
     *    operationId="index",
     *    @OA\Response(
     *       response=200,
     *       description="Successful operation",
     *       @OA\JsonContent(
     *           type="array",
     *           @OA\Items(ref="#/components/schemas/ExpenseResource")
     *       )
     *    )
     * )
     */

    public function index(): AnonymousResourceCollection
    {
        $expenses = Expense::query()
            ->where('user_id', Auth::user()->id)
            ->orderBy('id', 'desc')
            ->get();

        return ExpenseResource::collection($expenses);
    }

    /**
     * @OA\Post(
     *    path="/api/v1/expenses",
     *    security={{"bearerAuth": {}}},
     *    tags={"Expenses"},
     *    summary="Create a new expense",
     *    description="Create a new expense",
     *    operationId="store",
     *    @OA\RequestBody(
     *       required=true,
     *       @OA\JsonContent(
     *          required={"description", "date", "amount"},
     *          @OA\Property(property="description", type="string", example="Compra de material de escritório"),
     *          @OA\Property(property="date", type="string", format="date", example="2021-09-30"),
     *          @OA\Property(property="amount", type="number", format="float", example="100.00")
     *       )
     *    ),
     *    @OA\Response(
     *       response=201,
     *       description="Successful operation",
     *       @OA\JsonContent(ref="#/components/schemas/ExpenseResource")
     *    ),
     *    @OA\Response(
     *       response=400,
     *       description="Bad Request"
     *    )
     * )
     */

    public function store(ExpenseStoreRequest $request): Response
    {
        $data = $request->validated();

        $data['user_id'] = Auth::user()->id;

        $expense = Expense::query()->create($data);

        Auth::user()->notify(new ExpenseNotification($expense->user_id, $expense->id));

        return response(new ExpenseResource($expense), ResponseAlias::HTTP_CREATED);
    }

    /**
     * @OA\Get(
     *    path="/api/v1/expenses/{id}",
     *    security={{"bearerAuth": {}}},
     *    tags={"Expenses"},
     *    summary="Get expense by id",
     *    description="Get expense by id",
     *    operationId="show",
     *    @OA\Parameter(
     *       name="id",
     *       in="path",
     *       required=true,
     *       description="ID of expense",
     *       @OA\Schema(
     *          type="integer",
     *          format="int64"
     *       )
     *    ),
     *    @OA\Response(
     *       response=200,
     *       description="Successful operation",
     *       @OA\JsonContent(ref="#/components/schemas/ExpenseResource")
     *    ),
     *    @OA\Response(
     *       response=404,
     *       description="Expense not found"
     *    )
     * )
     */

    public function update(ExpenseUpdateRequest $request, Expense $expense): Response
    {
        $this->authorize('update', $expense);

        $data = $request->validated();

        $expense->query()->where('id', $expense->id)->update($data);

        $expense->refresh();

        return response(new ExpenseResource($expense));
    }

    /**
     * @OA\Delete(
     *    path="/api/v1/expenses/{id}",
     *    security={{"bearerAuth": {}}},
     *    tags={"Expenses"},
     *    summary="Delete expense by id",
     *    description="Delete expense by id",
     *    operationId="destroy",
     *    @OA\Parameter(
     *       name="id",
     *       in="path",
     *       required=true,
     *       description="ID of expense",
     *       @OA\Schema(
     *          type="integer",
     *          format="int64"
     *       )
     *    ),
     *    @OA\Response(
     *       response=204,
     *       description="Successful operation"
     *    ),
     *    @OA\Response(
     *       response=404,
     *       description="Expense not found"
     *    )
     * )
     */

    public function destroy(Expense $expense): Response
    {
        $expense->delete();

        return response()->noContent();
    }
}
