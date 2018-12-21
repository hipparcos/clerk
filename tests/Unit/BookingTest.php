<?php

namespace Tests\Unit;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Illuminate\Support\Facades\DB;
use App;
use DateTime;
use DateInterval;

class BookingTest extends TestCase
{
    use DatabaseMigrations;

    public function setUp() {
        parent::setUp();
        // Users.
        $this->james = factory(App\User::class)->create()->id;
        $this->ethan = factory(App\User::class)->create()->id;
        // Rooms.
        $this->tatooine = factory(App\Room::class)->create()->id;
        $this->gotham = factory(App\Room::class)->create()->id;
        // Instants.
        $this->now = new DateTime();
        $this->now_2haft   = (clone $this->now)->add(new DateInterval('PT2H'));
        $this->now_1haft   = (clone $this->now)->add(new DateInterval('PT1H'));
        $this->now_halfaft = (clone $this->now)->add(new DateInterval('PT30M'));
        $this->now_1hbef   = (clone $this->now)->sub(new DateInterval('PT1H'));
        $this->now_halfbef = (clone $this->now)->sub(new DateInterval('PT30M'));
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
                    'start' => $this->now,
                    'end' => $this->now_1haft,
            ])],
            [
                'case' => '0 minute long must not be valid',
                'saveable' => false,
                'booking' => factory(App\Booking::class)->make([
                    'user_id' => $this->james,
                    'room_id' => $this->tatooine,
                    'start' => $this->now,
                    'end' => $this->now,
            ])],
            [
                'case' => 'end < start must not be valid',
                'saveable' => false,
                'booking' => factory(App\Booking::class)->make([
                    'user_id' => $this->james,
                    'room_id' => $this->tatooine,
                    'start' => $this->now,
                    'end' => $this->now_1hbef,
            ])],
        ];

        foreach ($testcases as $tt) {
            $bk = $tt['booking'];
            // Try to save the booking to DB.
            $this->assertTrue(
                $tt['saveable'] === $bk->save(),
                'Case: '.$tt['case'].'.');
            // Check the DB.
            if ($tt['saveable']) {
                $this->assertDatabaseHas('bookings', [
                    'id' => $bk->id
                ]);
            } else {
                $this->assertDatabaseMissing('bookings', [
                    'id' => $bk->id
                ]);
            }
        }
    }

    public function testCheckRoomCollision() {
        // Fixtures.
        $booked = factory(App\Booking::class)->create([
            'user_id' => $this->james,
            'room_id' => $this->tatooine,
            'start' => $this->now,
            'end' => $this->now_1haft,
        ]);
        // Test cases.
        $testcases = [
            [
                'case' => 'different room, different time, must not collide',
                'collide' => false,
                'booking' => factory(App\Booking::class)->make([
                    'user_id' => $this->ethan,
                    'room_id' => $this->gotham,
                    'start' => $this->now,
                    'end' => $this->now_1haft,
            ])],
            [
                'case' => 'same room, before, must not collide',
                'collide' => false,
                'booking' => factory(App\Booking::class)->make([
                    'user_id' => $this->ethan,
                    'room_id' => $this->tatooine,
                    'start' => $this->now_1hbef,
                    'end' => $this->now,
            ])],
            [
                'case' => 'same room, after, must not collide',
                'collide' => false,
                'booking' => factory(App\Booking::class)->make([
                    'user_id' => $this->ethan,
                    'room_id' => $this->tatooine,
                    'start' => $this->now_1haft,
                    'end' => $this->now_2haft,
            ])],
            [
                'case' => 'same room, same time, must collide',
                'collide' => true,
                'booking' => factory(App\Booking::class)->make([
                    'user_id' => $this->ethan,
                    'room_id' => $this->tatooine,
                    'start' => $this->now,
                    'end' => $this->now_1haft,
            ])],
            [
                'case' => 'same room, end during meeting, must collide',
                'collide' => true,
                'booking' => factory(App\Booking::class)->make([
                    'user_id' => $this->ethan,
                    'room_id' => $this->tatooine,
                    'start' => $this->now_halfbef,
                    'end' => $this->now_halfaft,
            ])],
            [
                'case' => 'same room, start during meeting, must collide',
                'collide' => true,
                'booking' => factory(App\Booking::class)->make([
                    'user_id' => $this->ethan,
                    'room_id' => $this->tatooine,
                    'start' => $this->now_halfaft,
                    'end' => $this->now_2haft,
            ])],
            [
                'case' => 'same room, start before, finish after, must collide',
                'collide' => true,
                'booking' => factory(App\Booking::class)->make([
                    'user_id' => $this->ethan,
                    'room_id' => $this->tatooine,
                    'start' => $this->now_1hbef,
                    'end' => $this->now_2haft,
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

    public function testCheckUserCollision() {
        // Fixtures.
        $booked = factory(App\Booking::class)->create([
            'user_id' => $this->james,
            'room_id' => $this->tatooine,
            'start' => $this->now,
            'end' => $this->now_1haft,
        ]);
        // Test cases.
        $testcases = [
            [
                'case' => 'different user, same room, must not collide',
                'collide' => false,
                'booking' => factory(App\Booking::class)->make([
                    'user_id' => $this->ethan,
                    'room_id' => $this->gotham,
                    'start' => $this->now,
                    'end' => $this->now_1haft,
            ])],
            [
                'case' => 'different room, before, must not collide',
                'collide' => false,
                'booking' => factory(App\Booking::class)->make([
                    'user_id' => $this->james,
                    'room_id' => $this->gotham,
                    'start' => $this->now_1hbef,
                    'end' => $this->now,
            ])],
            [
                'case' => 'different room, after, must not collide',
                'collide' => false,
                'booking' => factory(App\Booking::class)->make([
                    'user_id' => $this->james,
                    'room_id' => $this->gotham,
                    'start' => $this->now_1haft,
                    'end' => $this->now_2haft,
            ])],
            [
                'case' => 'different room, same time, must collide',
                'collide' => true,
                'booking' => factory(App\Booking::class)->make([
                    'user_id' => $this->james,
                    'room_id' => $this->gotham,
                    'start' => $this->now,
                    'end' => $this->now_1haft,
            ])],
        ];
        // Tests.
        foreach ($testcases as $tt) {
            $bk = $tt['booking'];
            // Check collision.
            $this->assertTrue(
                $tt['collide'] === $bk->checkUserCollision(),
                'Case: '.$tt['case'].'.');
            // Check the DB.
            // Does not block save().
            $this->assertTrue(
                $bk->save(),
                'Case: '.$tt['case'].'.');
            $this->assertDatabaseHas('bookings', [
                'id' => $bk->id
            ]);
            $bk->delete();
        }
    }
}
