<?php

namespace Tests\Feature;

use App\Models\Courier;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CourierTest extends TestCase
{
    use RefreshDatabase;

    public function test_can_create_courier(): void
    {
        $data = [
            'name' => 'Budi Agung',
            'phone' => '081234567890',
            'email' => 'budi@example.com',
            'level' => 2,
            'registered_at' => '2026-08-12 10:00:00',
        ];

        $response = $this->postJson('/api/couriers', $data);

        $response
            ->assertStatus(201)
            ->assertJsonFragment([
                'name' => 'Budi Agung',
                'level' => 2,
            ]);

        $this->assertDatabaseHas('couriers', [
            'name' => 'Budi Agung',
            'email' => 'budi@example.com',
            'level' => 2,
        ]);
    }

    public function test_can_update_courier(): void
    {
        $courier = Courier::factory()->create([
            'name' => 'Budi',
            'level' => 2,
        ]);

        $response = $this->putJson(
            "/api/couriers/{$courier->id}",
            [
                'name' => 'Budi Agung',
                'level' => 3,
            ]
        );

        $response
            ->assertOk()
            ->assertJsonFragment([
                'name' => 'Budi Agung',
                'level' => 3,
            ]);

        $this->assertDatabaseHas('couriers', [
            'id' => $courier->id,
            'name' => 'Budi Agung',
            'level' => 3,
        ]);
    }

    public function test_can_delete_courier(): void
    {
        $courier = Courier::factory()->create();

        $response = $this->deleteJson(
            "/api/couriers/{$courier->id}"
        );

        $response->assertOk();

        $this->assertDatabaseMissing('couriers', [
            'id' => $courier->id,
        ]);
    }

    public function test_can_search_courier_by_multiple_name_terms(): void
    {
        Courier::factory()->create([
            'name' => 'Budiono Hadi Agung',
        ]);

        Courier::factory()->create([
            'name' => 'Citra Dewi',
        ]);

        $response = $this->getJson(
            '/api/couriers?search=budi+agung'
        );

        $response
            ->assertOk()
            ->assertJsonFragment([
                'name' => 'Budiono Hadi Agung',
            ]);
    }

    public function test_can_filter_couriers_by_level(): void
    {
        Courier::factory()->create([
            'name' => 'Level Two',
            'level' => 2,
        ]);

        Courier::factory()->create([
            'name' => 'Level Three',
            'level' => 3,
        ]);

        Courier::factory()->create([
            'name' => 'Level Five',
            'level' => 5,
        ]);

        $response = $this->getJson(
            '/api/couriers?level=2,3'
        );

        $response
            ->assertOk()
            ->assertJsonFragment([
                'name' => 'Level Two',
            ])
            ->assertJsonFragment([
                'name' => 'Level Three',
            ])
            ->assertJsonMissing([
                'name' => 'Level Five',
            ]);
    }
}
