<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PanitiaAccessTest extends TestCase
{
    use RefreshDatabase;

    public function test_operator_cannot_access_dashboard(): void
    {
        $user = User::factory()->create(['role' => 'operator']);
        $this->actingAs($user);

        $response = $this->get('/dashboard');

        $response->assertStatus(403);
    }

    public function test_operator_can_access_kiosk_page(): void
    {
        $user = User::factory()->create(['role' => 'operator']);
        $this->actingAs($user);

        $response = $this->get('/absensi/kiosk');

        $response->assertStatus(200);
    }
}
