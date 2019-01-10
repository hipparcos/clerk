<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use DateTime;
use DateInterval;
use Laravel\Passport\Passport;
use Carbon\Carbon;

class BookingTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp() {
        parent::setUp();
        // Users.
        $this->james = factory(\App\User::class)->create();
        $this->ethan = factory(\App\User::class)->create();
        // Rooms.
        $this->tatooine = factory(\App\Room::class)->create(['name' => 'tatooine']);
        $this->gotham   = factory(\App\Room::class)->create(['name' => 'gotham']);
        // Instants.
        $now = Carbon::now();
        $now->minute = 0;
        $now->second = 0;
        $this->inst0 = (clone $now)->hour(10);
        $this->inst1 = (clone $now)->hour(11);
        $this->inst2 = (clone $now)->hour(12);
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
        $this->bk2 = factory(\App\Booking::class)->create([
            'user_id' => $this->james->id,
            'room_id' => $this->tatooine->id,
            'start'   => $this->inst0,
            'end'     => $this->inst1,
        ]);
        // Historic booking.
        $this->bk3 = factory(\App\Booking::class)->create([
            'user_id' => $this->james->id,
            'room_id' => $this->tatooine->id,
            'start'   => Carbon::create(2000, 1, 1, 10, 0, 0),
            'end'     => Carbon::create(2000, 1, 1, 11, 0, 0),
        ]);
    }

    public function testIndexNotAuthenticated() {
        $response = $this->getJson('/api/bookings');

        $response->assertStatus(401);
    }

    public function testIndexToday() {
        Passport::actingAs($this->ethan, ['*']);

        $response = $this->getJson('/api/bookings');

        $response
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    [ 'id' => $this->bk0->id ], // gotham, 10h
                    [ 'id' => $this->bk2->id ], // tatooine, 10h
                    [ 'id' => $this->bk1->id ], // tatooine, 11h
                ],
            ]);
    }

    public function testIndexHistoric() {
        Passport::actingAs($this->ethan, ['*']);

        $response = $this->getJson('/api/bookings/2000/01/01');

        $response
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    [ 'id' => $this->bk3->id ],
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
            ->assertStatus(409)
            ->assertJsonValidationErrors('data.relationships.room.data.id');
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
            ->assertStatus(409)
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

    public function testUpdate() {
        Passport::actingAs($this->ethan, ['*']);

        $id = $this->bk0->id;
        $duration = 120;
        $response = $this
            ->patchJson('/api/bookings/'.$id, [
                'data' => [
                    'type' => 'booking',
                    'id' => $id,
                    'attributes' => [
                        'start' => $this->inst2->format('Y-m-d H:i:s'),
                        'duration' => $duration,
                    ],
                    'relationships' => [
                        'room' => [
                            'data' => ['id' => $this->tatooine->id],
                        ],
                    ],
                ],
            ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $id,
                    'attributes' => [
                        'end' => (clone $this->inst2)->addMinutes($duration)->jsonSerialize(),
                    ],
                ],
            ]);
    }

    public function testUpdateDurationOnly() {
        Passport::actingAs($this->ethan, ['*']);

        $id = $this->bk0->id;
        $duration = 40;
        $response = $this
            ->patchJson('/api/bookings/'.$id, [
                'data' => [
                    'type' => 'booking',
                    'id' => $id,
                    'attributes' => [
                        'start' => $this->inst0->format('Y-m-d H:i:s'),
                        'duration' => $duration,
                    ],
                    'relationships' => [
                        'room' => [
                            'data' => ['id' => $this->gotham->id],
                        ],
                    ],
                ],
            ]);

        $response
            ->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $id,
                    'attributes' => [
                        'end' => (clone $this->inst0)->addMinutes($duration)->jsonSerialize(),
                    ],
                ],
            ]);
    }

    public function testUpdateRoomCollision() {
        Passport::actingAs($this->ethan, ['*']);

        $id = $this->bk0->id;
        $duration = 60;
        $response = $this
            ->patchJson('/api/bookings/'.$id, [
                'data' => [
                    'type' => 'booking',
                    'id' => $id,
                    'attributes' => [
                        'start' => $this->inst0->format('Y-m-d H:i:s'),
                        'duration' => $duration,
                    ],
                    'relationships' => [
                        'room' => [
                            'data' => ['id' => $this->tatooine->id],
                        ],
                    ],
                ],
            ]);

        $response
            ->assertStatus(409)
            ->assertJsonValidationErrors('data.relationships.room.data.id');
    }

    public function testUpdateNotOwned() {
        Passport::actingAs($this->james, ['*']);

        $id = $this->bk0->id;
        $duration = 120;
        $response = $this
            ->patchJson('/api/bookings/'.$id, [
                'data' => [
                    'type' => 'booking',
                    'id' => $id,
                    'attributes' => [
                        'start' => $this->inst2->format('Y-m-d H:i:s'),
                        'duration' => $duration,
                    ],
                    'relationships' => [
                        'room' => [
                            'data' => ['id' => $this->tatooine->id],
                        ],
                    ],
                ],
            ]);

        $response->assertStatus(404);
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
