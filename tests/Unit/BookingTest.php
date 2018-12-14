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

    public function testValidate() {
        $testcases = [
            [
                'case' => 'happy path',
                'valid' => true,
                'booking' => factory(App\Booking::class)->create([
                    'user_id' => $this->james,
                    'room_id' => $this->tatooine,
                    'start' => new DateTime(),
                    'start' => (new DateTime())->add(new DateInterval('PT1H')),
            ])],
            [
                'case' => '0 minute long must not be valid',
                'valid' => false,
                'booking' => factory(App\Booking::class)->create([
                    'user_id' => $this->james,
                    'room_id' => $this->tatooine,
                    'start' => new DateTime(),
                    'start' => new DateTime(),
            ])],
            [
                'case' => 'end < start must not be valid',
                'valid' => false,
                'booking' => factory(App\Booking::class)->create([
                    'user_id' => $this->james,
                    'room_id' => $this->tatooine,
                    'start' => new DateTime(),
                    'start' => (new DateTime())->sub(new DateInterval('PT1H')),
            ])],
        ];

        foreach ($testcases as $tt) {
            $this->assertTrue(
                $tt['valid'] === $tt['booking']->validate(), 
                'Case: '.$tt['case'].'.');
        }
    }
}
