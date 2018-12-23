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
                    [ 'id' => $this->bk0->id ],
                    [ 'id' => $this->bk1->id ],
                ],
            ]);
    }

    public function testShowNotAuthenticated() {
        $response = $this->getJson('/api/bookings/'.$this->bk0->id);

        $response->assertStatus(401);
    }

    public function testShowAuthenticated() {
        Passport::actingAs($this->ethan, ['*']);

        $response = $this->getJson('/api/bookings/'.$this->bk0->id);

        $response
            ->assertStatus(200)
            ->assertJson(['data' => ['id' => $this->bk0->id]]);
    }

    public function testShowNotFound() {
        Passport::actingAs($this->ethan, ['*']);

        $response = $this->getJson('/api/bookings/1000');

        $response->assertStatus(404);
    }

    public function testCreateNotAuthenticated() {
        $response = $this
            ->postJson('/api/bookings', [
                'data' => [
                    'attributes' => [
                        'start' => $this->inst1->format('Y-m-d H:i:s'),
                        'duration' => 60,
                    ],
                    'relationships' => [
                        'room' => [
                            'data' => ['id' => $this->gotham->id],
                        ],
                    ],
                ],
            ]);

        $response->assertStatus(401);
    }

    public function testCreateAuthenticated() {
        Passport::actingAs($this->james, ['*']);

        $response = $this
            ->postJson('/api/bookings', [
                'data' => [
                    'attributes' => [
                        'start' => $this->inst1->format('Y-m-d H:i:s'),
                        'duration' => 60,
                    ],
                    'relationships' => [
                        'room' => [
                            'data' => ['id' => $this->gotham->id],
                        ],
                    ],
                ],
            ]);

        $response->assertStatus(201);
        $data = $response->json()['data'];
        $this->assertDatabaseHas('bookings', [
            'id' => $data['id'],
        ]);
    }

    public function testCreateAuthenticatedRoomCollision() {
        Passport::actingAs($this->james, ['*']);

        $response = $this
            ->postJson('/api/bookings', [
                'data' => [
                    'attributes' => [
                        'start' => $this->inst1->format('Y-m-d H:i:s'),
                        'duration' => 60,
                    ],
                    'relationships' => [
                        'room' => [
                            'data' => ['id' => $this->tatooine->id],
                        ],
                    ],
                ],
            ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors('data.attributes.room.data.id');
    }

    public function testCreateAuthenticatedUserCollision() {
        Passport::actingAs($this->ethan, ['*']);

        $response = $this
            ->postJson('/api/bookings', [
                'data' => [
                    'attributes' => [
                        'start' => $this->inst1->format('Y-m-d H:i:s'),
                        'duration' => 60,
                    ],
                    'relationships' => [
                        'room' => [
                            'data' => ['id' => $this->gotham->id],
                        ],
                    ],
                ],
            ]);

        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors('data.attributes.start');
    }

    public function testCreateAuthenticatedUserCollisionOverride() {
        Passport::actingAs($this->ethan, ['*']);

        $response = $this
            ->postJson('/api/bookings', [
                'data' => [
                    'attributes' => [
                        'start' => $this->inst1->format('Y-m-d H:i:s'),
                        'duration' => 60,
                    ],
                    'relationships' => [
                        'room' => [
                            'data' => ['id' => $this->gotham->id],
                        ],
                    ],
                ],
                'meta' => [
                    'overrideUserCollision' => true,
                ],
            ]);

        $response->assertStatus(201);
        $data = $response->json()['data'];
        $this->assertDatabaseHas('bookings', [
            'id' => $data['id'],
        ]);
    }

    public function testDestroy() {
        Passport::actingAs($this->ethan, ['*']);

        $response = $this->deleteJson('/api/bookings/'.$this->bk0->id);

        $response->assertStatus(200);
    }

    public function testDestroyNotOwned() {
        Passport::actingAs($this->james, ['*']);

        $response = $this->deleteJson('/api/bookings/'.$this->bk1->id);

        $response->assertStatus(404);
    }
}
