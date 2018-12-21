<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use DateTime;
use DateInterval;
use Laravel\Passport\Passport;

class BookingTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp() {
        parent::setUp();
        // Users.
        $this->james = factory(\App\User::class)->create();
        $this->ethan = factory(\App\User::class)->create();
        // Rooms.
        $this->tatooine = factory(\App\Room::class)->create();
        $this->gotham   = factory(\App\Room::class)->create();
        // Instants.
        $this->inst0 = new DateTime('01/01/2000 10:00:00');
        $this->inst1 = new DateTime('01/01/2000 11:00:00');
        $this->inst2 = new DateTime('01/01/2000 12:00:00');
        // Bookings.
        $this->bk0 = factory(\App\Booking::class)->create([
            'user_id' => $this->ethan->id,
            'room_id' => $this->gotham->id,
            'start'   => $this->inst0,
            'end'     => $this->inst1,
        ]);
        $this->bk1 = factory(\App\Booking::class)->create([
            'user_id' => $this->ethan->id,
            'room_id' => $this->tatooine->id,
            'start'   => $this->inst1,
            'end'     => $this->inst2,
        ]);
        factory(\App\Booking::class)->create([
            'user_id' => $this->james->id,
            'room_id' => $this->tatooine->id,
            'start'   => $this->inst0,
            'end'     => $this->inst1,
        ]);
    }

    public function testIndexNotAuthenticated() {
        $response = $this->getJson('/api/bookings');

        $response->assertStatus(401);
    }

    public function testIndexAuthenticated() {
        Passport::actingAs($this->ethan, ['*']);

        $response = $this->getJson('/api/bookings');

        $response
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    [
                        'name' => $this->gotham->name,
                        'start' => $this->bk0->start,
                        'end' => $this->bk0->end,
                    ],
                    [
                        'name' => $this->tatooine->name,
                        'start' => $this->bk1->start,
                        'end' => $this->bk1->end,
                    ],
                ],
            ]);
    }

    public function testCreateNotAuthenticated() {
        $response = $this
            ->postJson('/api/bookings', [
                'room_id' => $this->gotham->id,
                'start' => $this->inst1->format('Y-m-d H:i:s'),
                'end' => 60,
            ]);

        $response->assertStatus(401);
    }

    public function testCreateAuthenticated() {
        Passport::actingAs($this->james, ['*']);

        $response = $this
            ->postJson('/api/bookings', [
                'room_id' => $this->gotham->id,
                'start' => $this->inst1->format('Y-m-d H:i:s'),
                'end' => 60,
            ]);

        $response->assertStatus(201);
        $this->assertDatabaseHas('bookings', [
            'id' => $response->json()['data']['id'],
            'room_id' => $this->gotham->id,
        ]);
    }

    public function testCreateAuthenticatedInvalid() {
        Passport::actingAs($this->ethan, ['*']);

        $response = $this
            ->postJson('/api/bookings', [
                'room_id' => $this->tatooine->id,
                'start' => $this->inst0->format('Y-m-d H:i:s'),
                'end' => 60,
            ]);

        $response->assertStatus(422);
    }
}
