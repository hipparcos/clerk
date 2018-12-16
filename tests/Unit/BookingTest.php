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
        // Users.
        $this->james = factory(App\User::class)->create()->id;
        $this->ethan = factory(App\User::class)->create()->id;
        // Rooms.
        $this->tatooine = factory(App\Room::class)->create()->id;
        $this->gotham = factory(App\Room::class)->create()->id;
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

    public function testCheckRoomCollision() {
        $now = new DateTime();
        $now_2haft   = (clone $now)->add(new DateInterval('PT2H'));
        $now_1haft   = (clone $now)->add(new DateInterval('PT1H'));
        $now_halfaft = (clone $now)->add(new DateInterval('PT30M'));
        $now_1hbef   = (clone $now)->sub(new DateInterval('PT1H'));
        $now_halfbef = (clone $now)->sub(new DateInterval('PT30M'));
        // Fixtures.
        $booked = factory(App\Booking::class)->create([
            'user_id' => $this->james,
            'room_id' => $this->tatooine,
            'start' => $now,
            'end' => $now_1haft,
        ]);
        // Test cases.
        $testcases = [
            [
                'case' => 'different room, different time, must not collide',
                'collide' => false,
                'booking' => factory(App\Booking::class)->make([
                    'user_id' => $this->ethan,
                    'room_id' => $this->gotham,
                    'start' => $now,
                    'end' => $now_1haft,
            ])],
            [
                'case' => 'same room, before, must not collide',
                'collide' => false,
                'booking' => factory(App\Booking::class)->make([
                    'user_id' => $this->ethan,
                    'room_id' => $this->tatooine,
                    'start' => $now_1hbef,
                    'end' => $now,
            ])],
            [
                'case' => 'same room, after, must not collide',
                'collide' => false,
                'booking' => factory(App\Booking::class)->make([
                    'user_id' => $this->ethan,
                    'room_id' => $this->tatooine,
                    'start' => $now_1haft,
                    'end' => $now_2haft,
            ])],
            [
                'case' => 'same room, same time, must collide',
                'collide' => true,
                'booking' => factory(App\Booking::class)->make([
                    'user_id' => $this->ethan,
                    'room_id' => $this->tatooine,
                    'start' => $now,
                    'end' => $now_1haft,
            ])],
            [
                'case' => 'same room, end during meeting, must collide',
                'collide' => true,
                'booking' => factory(App\Booking::class)->make([
                    'user_id' => $this->ethan,
                    'room_id' => $this->tatooine,
                    'start' => $now_halfbef,
                    'end' => $now_halfaft,
            ])],
            [
                'case' => 'same room, start during meeting, must collide',
                'collide' => true,
                'booking' => factory(App\Booking::class)->make([
                    'user_id' => $this->ethan,
                    'room_id' => $this->tatooine,
                    'start' => $now_halfaft,
                    'end' => $now_2haft,
            ])],
        ];
        // Tests.
        foreach ($testcases as $tt) {
            $bk = $tt['booking'];
            // Check collision.
            $this->assertTrue(
                $tt['collide'] === $bk->checkRoomCollision(),
                'Case: '.$tt['case'].'.');

            // Check the DB.
            $this->assertTrue(
                !$tt['collide'] === $bk->save(),
                'Case: '.$tt['case'].'.');
            if (!$tt['collide']) {
                $this->assertDatabaseHas('bookings', [
                    'id' => $bk->id
                ]);
            } else {
                $this->assertDatabaseMissing('bookings', [
                    'id' => $bk->id
                ]);
            }
            $bk->delete();
        }
    }
}
