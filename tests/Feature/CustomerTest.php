<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Customer;
use App\Models\TemporaryCustomer;
use Illuminate\Foundation\Testing\RefreshDatabase;

/**
 * Customer test
 */
class CustomerTest extends TestCase
{
    use RefreshDatabase;

    /**
     * @return void
     */
    public function test_login_admin_can_create_customer(): void
    {
        $user = User::factory()->create();
        $token = \JWTAuth::fromUser($user);
        $response = $this->postJson('/api/v1/customers?token='.$token, [
            'email' =>  'orutu1@gmail.com',
            'firstName' => 'Orutu' ,
            'lastName' => 'Akposieyefa'
        ]);
        $response->assertStatus(201);
   }

    /**
     * @return void
     */
    public function test_login_admin_can_create_customer_fail(): void
    {
        $user = User::factory()->create();
        $token = \JWTAuth::fromUser($user);
        $response = $this->postJson('/api/v1/customers?token='.$token, [
            'email' =>  'orutu10@gmail.com',
            'fristName' => '' ,
            'lastName' => 'Akposieyefa'
        ]);
        $response->assertStatus(422);
   }

    /**
     * @return void
     */
    public function test__admin_can_create_customer_when_not_logged_in(): void
    {
        $response = $this->postJson('/api/v1/customers', [
            'email' =>  'orutu10@gmail.com',
            'fristName' => 'Orutu' ,
            'lastName' => 'Akposieyefa'
        ]);
        $response->assertStatus(401);
   }

    /**
     * @return void
     */
    public function test__admin_can_get_all_customer_logged_in(): void
    {
        $user = User::factory()->create();
        $token = \JWTAuth::fromUser($user);
        $response = $this->getJson('/api/v1/customers?token='.$token);
        $response->assertStatus(200);
    }

    /**
     * @return void
     */
    public function test__admin_can_get_single_customer_logged_in(): void
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->create();
        $token = \JWTAuth::fromUser($user);
        $response = $this->getJson('/api/v1/customers/'.$customer->slug.'?token='.$token);
        $response->assertStatus(200);
    }

    /**
     * @return void
     */
    public function test__admin_can_get_pending_request_from_other_admins_when_logged_in(): void
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->create();
        $token = \JWTAuth::fromUser($user);
        $response = $this->getJson('/api/v1/customers-request?token='.$token);
        $response->assertStatus(200);
    }

    /**
     * @return void
     */
    public function test__admin_can_get_can_delete_customer(): void
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->create();
        $token = \JWTAuth::fromUser($user);
        $response = $this->deleteJson('/api/v1/customers/'.$customer->slug.'?token='.$token);
        $response->assertStatus(200);
    }

    /**
     * @return void
     */
    public function test__admin_can_get_can_update_customer(): void
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->create();
        $token = \JWTAuth::fromUser($user);
        $response = $this->patchJson('/api/v1/customers/'.$customer->slug.'?token='.$token, [
            'email' =>  'orutu10@gmail.com',
            'firstName' => 'Orutu' ,
            'lastName' => 'Akposieyefa'
        ]);
        $response->assertStatus(200);
    }

    /**
     * @return void
     */
    public function test__admin_can_get_can_approve_request(): void
    {
        $faker = \Faker\Factory::create();
        $customer = User::factory()->create();
        $customer = TemporaryCustomer::factory()->create();
        $user = User::factory()->create();
        $token = \JWTAuth::fromUser($user);
        $response = $this->postJson('/api/v1/approve/'.$customer->slug.'?token='.$token, [
            'approval_status' => 'approved',
            'operation' => 'create'
        ]);
        $response->assertStatus(200);
    }

    /**
     * @return void
     */
    public function test__admin_can_get_can_not_approve_his_own_request(): void
    {
        $faker = \Faker\Factory::create();
        $user = User::factory()->create();
        $token = \JWTAuth::fromUser($user);
        $customer = TemporaryCustomer::factory()->create();
        $response = $this->postJson('/api/v1/approve/'.$customer->slug.'?token='.$token, [
            'approval_status' => 'approved',
            'operation' => 'create'
        ]);
        $response->assertStatus(406);
    }

    /**
     * @return void
     */
    public function test__admin_can_get_can_approve_request_returns_validation_error(): void
    {
        $user = User::factory()->create();
        $customer = TemporaryCustomer::factory()->create();
        $token = \JWTAuth::fromUser($user);
        $response = $this->postJson('/api/v1/approve/'.$customer->slug.'?token='.$token, [
            'approval_status' => 'approved',
            'operation' => ''
        ]);
        $response->assertStatus(422);
    }

}
