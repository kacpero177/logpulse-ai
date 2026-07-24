<?php

namespace Tests\Feature\Api;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class LogTest extends TestCase
{
    use RefreshDatabase;

    public function test_unauthorized_user_cannot_submit_logs(): void
    {
        $response = $this->postJson('/api/v1/logs', [
            'service_name' => 'AuthService',
            'level' => 'error',
            'message' => 'Unauthorized access test',
        ]);

        $response->assertStatus(401);
    }

    public function test_authorized_user_can_submit_log_successfully(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user, 'sanctum')->postJson('/api/v1/logs', [
            'service_name' => 'OrderService',
            'level' => 'critical',
            'message' => 'Out of stock exception',
            'stack_trace' => 'OutOfStockException at OrderController.php:42',
        ]);

        $response->assertStatus(202)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Log received and queued for AI analysis.',
                 ]);

        $this->assertDatabaseHas('logs', [
            'service_name' => 'OrderService',
            'level' => 'critical',
            'message' => 'Out of stock exception',
        ]);
    }
}