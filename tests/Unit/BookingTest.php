<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use App;
use DateTime;
use DateInterval;

class BookingTest extends TestCase
{
    use RefreshDatabase;

    public function setUp() {
        parent::setUp();
        $this->james = factory(App\User::class)->create()->id;
        $this->tatooine = factory(App\Room::class)->create()->id;
    }

    public function testSave() {
        $now = new DateTime();
        $testcases = [
            [
                'case' => 'happy path',
                'saveable' => true,
                'booking' => factory(App\Booking::class)->make([
                    'user_id' => $this->james,
                    'room_id' => $this->tatooine,
                    'start' => new DateTime(),
                    'end' => (new DateTime())->add(new DateInterval('PT1H')),
            ])],
            [
                'case' => '0 minute long must not be valid',
                'saveable' => false,
                'booking' => factory(App\Booking::class)->make([
                    'user_id' => $this->james,
                    'room_id' => $this->tatooine,
                    'start' => $now,
                    'end' => $now,
            ])],
            [
                'case' => 'end < start must not be valid',
                'saveable' => false,
                'booking' => factory(App\Booking::class)->make([
                    'user_id' => $this->james,
                    'room_id' => $this->tatooine,
                    'start' => new DateTime(),
                    'end' => (new DateTime())->sub(new DateInterval('PT1H')),
            ])],
        ];

        foreach ($testcases as $tt) {
            // Try to save the booking to DB.
            $this->assertTrue(
                $tt['saveable'] === $tt['booking']->save(),
                'Case: '.$tt['case'].'.');
            // Check the DB.
            if ($tt['saveable']) {
                $this->assertDatabaseHas('bookings', [
                    'id' => $tt['booking']->id
                ]);
            } else {
                $this->assertDatabaseMissing('bookings', [
                    'id' => $tt['booking']->id
                ]);
            }
        }
    }
}
