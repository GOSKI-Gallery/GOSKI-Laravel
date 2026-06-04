<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class NotificationsControllerTest extends TestCase
{
    use RefreshDatabase;

    public function test_notifications_index_requires_authentication()
    {
        $response = $this->get('/notifications');

        $response->assertRedirect('/login');
    }

    public function test_notifications_index_returns_json()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->get('/notifications');

        $response->assertOk();
        $response->assertJson([]);
    }

    public function test_notifications_mark_as_read_returns_json()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->post('/notifications/read');

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'message' => 'Notificações marcadas como lidas.',
        ]);
    }

    public function test_notifications_delete_returns_json()
    {
        $user = User::factory()->create();

        $response = $this->actingAs($user)->delete('/notifications/some-id');

        $response->assertOk();
        $response->assertJson([
            'success' => true,
            'message' => 'Notificação removida.',
        ]);
    }
}
