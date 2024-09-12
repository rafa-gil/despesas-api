<?php

namespace Tests\Feature;

use App\Models\User;
use PHPUnit\Framework\Attributes\Test;
use App\Notifications\ExpenseNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;
use Tests\TestCase;

class ExpenseControllerTest extends TestCase
{
    use RefreshDatabase;

    #[Test]
    public function it_should_create_expense(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->post(route('expenses.store'), [
            'description' => 'Test Expense',
            'date'        => now()->format('Y-m-d'),
            'amount_in_cents' => 10000,
        ]);

        $this->assertDatabaseHas('expenses', [
            'description' => 'Test Expense',
            'date'        => now()->format('Y-m-d'),
            'amount_in_cents' => 1000000,
            'user_id'     => $user->id,
        ]);

        $response->assertStatus(ResponseAlias::HTTP_CREATED);
    }

    #[Test]
    public function it_should_create_expense_send_notification(): void
    {
        Notification::fake();

        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $response = $this->post(route('expenses.store'), [
            'description' => 'Test Expense',
            'date'        => now()->format('Y-m-d'),
            'amount_in_cents' => 10000,
        ]);

        $this->assertDatabaseHas('expenses', [
            'description' => 'Test Expense',
            'date'        => now()->format('Y-m-d'),
            'amount_in_cents' => 1000000,
            'user_id'     => $user->id,
        ]);

        $response->assertStatus(ResponseAlias::HTTP_CREATED);

        Notification::assertSentTo($user, ExpenseNotification::class);
    }

    #[Test]
    public function it_should_update_expense(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $expense = $user->expenses()->create([
            'description' => 'Test Expense',
            'date'        => now()->format('Y-m-d'),
            'amount_in_cents' => 10000,
        ]);

        $response = $this->put(route('expenses.update', $expense), [
            'description' => 'Updated Test Expense',
            'date'        => now()->format('Y-m-d'),
            'amount_in_cents' => 20000,
        ]);

        $this->assertDatabaseHas('expenses', [
            'description' => 'Updated Test Expense',
            'date'        => now()->format('Y-m-d'),
            'amount_in_cents' => 20000,
            'user_id'     => $user->id,
        ]);

        $response->assertStatus(ResponseAlias::HTTP_OK);
    }

    #[Test]
    public function it_should_not_allow_update_expense_another_user(): void
    {
        $user = User::factory()->create();

        $user2 = User::factory()->create();

        Sanctum::actingAs($user2);

        $expense = $user->expenses()->create([
            'description' => 'Test Expense',
            'date'        => now()->format('Y-m-d'),
            'amount_in_cents' => 10000,
        ]);

        $response = $this->put(route('expenses.update', $expense), [
            'description' => 'Updated Test Expense',
            'date'        => now()->format('Y-m-d'),
            'amount_in_cents' => 20000,
        ]);

        $response->assertStatus(ResponseAlias::HTTP_FORBIDDEN);
    }

    #[Test]
    public function it_should_delete_expense(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $expense = $user->expenses()->create([
            'description' => 'Test Expense',
            'date'        => now()->format('Y-m-d'),
            'amount_in_cents' => 10000,
        ]);

        $response = $this->delete(route('expenses.destroy', $expense));

        $response->assertStatus(ResponseAlias::HTTP_NO_CONTENT);

        $this->assertDatabaseMissing('expenses', [
            'id' => $expense->id,
        ]);
    }

    #[Test]
    public function it_should_get_expenses(): void
    {
        $user = User::factory()->create();

        Sanctum::actingAs($user);

        $this->get(route('expenses.index'))->assertOk();
    }
}
