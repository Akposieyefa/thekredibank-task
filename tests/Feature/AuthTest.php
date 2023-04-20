<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

/**
 * Auth test
 */
class AuthTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function test_login_to_application_is_successful(): void
    {
        $user = User::factory()->create();
        $response = $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'password'
        ]);
        $response->assertStatus(200);
    }

    /**
     * @return void
     */
    public function test_login_to_application_failed(): void
    {
        $user = User::factory()->create();
        $response = $this->postJson('/api/v1/login', [
            'email' => $user->email,
            'password' => 'Password@123'
        ]);
        $response->assertStatus(401);
    }

    /**
     * @return void
     */
    public function test_check_user_profile_successful(): void
    {
        $user = User::factory()->create();
        $token = \JWTAuth::fromUser($user);
        $response = $this->getJson('/api/v1/profiles?token='.$token);
        $response->assertStatus(200);
    }

    /**
     * @return void
     */
    public function test_logout_route_successful(): void
    {
        $user = User::factory()->create();
        $token = \JWTAuth::fromUser($user);
        $response = $this->postJson('/api/v1/logout?token='.$token);
        $response->assertStatus(200);
    }

}
