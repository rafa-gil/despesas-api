<?php

use App\Models\User;
use Laravel\Sanctum\Sanctum;

it('should login successfully', function () {
    $user = User::factory()->createOne();

    $response = $this->postJson('/api/login', [
        'email'    => $user->email,
        'password' => 'password',
    ]);

    $response->assertStatus(200);
});

it('should not login with invalid credentials', function () {
    $response = $this->postJson('/api/login', [
        'email'    => fake()->email,
        'password' => 'password',
    ]);

    $response->assertStatus(401);
    expect($response->json('message'))->toBe('Unauthorized');
});

it('should logout successfully', function () {
    $user = User::factory()->createOne();

    Sanctum::actingAs($user);

    $response = $this->getJson('/api/logout');

    $response->assertStatus(200);
    expect($response->json('message'))->toBe('Logout successfully');
});

it('should logout with invalid token', function () {
    $response = $this->getJson('/api/logout');

    $response->assertStatus(401);
});

it('should register successfully', function () {
    $response = $this->postJson('/api/register', [
        'name'     => fake()->name,
        'email'    => fake()->email,
        'password' => 'password',
    ]);

    $response->assertStatus(201);
    expect($response->json('message'))->toBe('Successfully created');
});

it('should not register with invalid data', function () {
    $response = $this->postJson('/api/register', [
        'name'  => fake()->name,
        'email' => fake()->email,
    ]);

    $response->assertStatus(422);
    expect($response->json('message'))->toBe('The password field is required.');

    $response = $this->postJson('/api/register', [
        'name'     => fake()->name,
        'password' => 'password',
    ]);

    $response->assertStatus(422);
    expect($response->json('message'))->toBe('The email field is required.');

    $response = $this->postJson('/api/register', [
        'email'    => fake()->email,
        'password' => 'password',
    ]);

    $response->assertStatus(422);
    expect($response->json('message'))->toBe('The name field is required.');
});
