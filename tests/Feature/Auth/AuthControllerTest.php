<?php

namespace Tests\Feature\Auth;

use PHPUnit\Framework\Attributes\Test;
use App\Models\User;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class AuthControllerTest extends TestCase
{
    #[Test]
    public function it_should_login_successfully()
    {
        $user = User::factory()->createOne();

        $response = $this->postJson('/api/login', [
            'email'    => $user->email,
            'password' => 'password',
        ]);

        $response->assertStatus(200);
    }

    #[Test]
    public function it_should_not_login_with_invalid_credentials()
    {
        $response = $this->postJson('/api/login', [
            'email'    => fake()->email,
            'password' => 'password',
        ]);

        $response->assertStatus(401);

        $this->containsEqual('Unauthorized', $response->json('message'));
    }

    #[Test]
    public function it_should_logout_successfully()
    {
        $user = User::factory()->createOne();

        Sanctum::actingAs($user);

        $response = $this->getJson('/api/logout');

        $response->assertStatus(200);

        $this->containsEqual('Logout successfully', $response->json('message'));
    }

    #[Test]
    public function it_should_logout_with_invalid_token()
    {
        $response = $this->getJson('/api/logout');

        $response->assertStatus(401);
    }

    #[Test]
    public function it_should_register_successfully()
    {
        $response = $this->postJson('/api/register', [
            'name'     => fake()->name,
            'email'    => fake()->email,
            'password' => 'password',
        ]);

        $response->assertStatus(201);

        $this->containsEqual('Successfully created', $response->json('message'));
    }

    #[Test]
    public function it_should_not_register_with_invalid_data()
    {
        $response = $this->postJson('/api/register', [
            'name'  => fake()->name,
            'email' => fake()->email,
        ]);

        $response->assertStatus(422);

        $this->containsEqual('The password field is required.', $response->json('message'));

        $response = $this->postJson('/api/register', [
            'name'     => fake()->name,
            'password' => 'password',
        ]);

        $response->assertStatus(422);

        $this->containsEqual('The email field is required.', $response->json('message'));

        $response = $this->postJson('/api/register', [
            'email'    => fake()->email,
            'password' => 'password',
        ]);

        $response->assertStatus(422);

        $this->containsEqual('The name field is required.', $response->json('message'));
    }
}
