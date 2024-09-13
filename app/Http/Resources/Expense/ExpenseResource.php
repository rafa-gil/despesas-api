<?php

namespace App\Http\Resources\Expense;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ExpenseResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    /**
 * @OA\Schema(
 *    schema="ExpenseResource",
 *    type="object",
 *    title="Expense Resource",
 *    description="Expense Resource model",
 *    @OA\Property(
 *       property="id",
 *       type="integer",
 *       description="ID da despesa"
 *    ),
 *    @OA\Property(
 *       property="description",
 *       type="string",
 *       description="Descrição da despesa"
 *    ),
 *    @OA\Property(
 *       property="amount",
 *       type="number",
 *       format="float",
 *       description="Valor da despesa"
 *    ),
 *    @OA\Property(
 *       property="created_at",
 *       type="string",
 *       format="date-time",
 *       description="Data de criação da despesa"
 *    ),
 *    @OA\Property(
 *       property="updated_at",
 *       type="string",
 *       format="date-time",
 *       description="Data de atualização da despesa"
 *    )
 * )
 */
    public function toArray(Request $request): array
    {
        return parent::toArray($request);
    }
}
