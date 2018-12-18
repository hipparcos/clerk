<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use DateTime;
use DateInterval;

class BookingTest extends TestCase
{
    use RefreshDatabase;

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

    public function testIndex() {
        // Not authenticated: must be rejected.
        $response = $this
            ->get('/bookings');
        $response->assertStatus(302);

        // Authenticated: must return a list of all bookings for the current user.
        $response = $this
            ->actingAs($this->ethan)
            ->get('/bookings');
        $response
            ->assertStatus(200)
            ->assertJson([
                'bookings' => [
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
}
