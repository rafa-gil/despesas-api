<?php

use App\Models\User;
use App\Notifications\ExpenseNotification;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Notification;
use Laravel\Sanctum\Sanctum;
use Symfony\Component\HttpFoundation\Response as ResponseAlias;

uses(RefreshDatabase::class);

it('should create expense', function () {
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
});

it('should create expense and send notification', function () {
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
});

it('should update expense', function () {
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
});

it('should not allow update expense another user', function () {
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
});

it('should delete expense', function () {
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
});

it('should get expenses', function () {
    $user = User::factory()->create();

    Sanctum::actingAs($user);

    $this->get(route('expenses.index'))->assertOk();
});
