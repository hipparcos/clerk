<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseMigrations;
use Laravel\Passport\Passport;

class UserRegistrationTest extends TestCase
{
    use DatabaseMigrations;

    public function testUserRegistration() {
        $response = $this->postJson('/api/register', [
            'data' => [
                'type' => 'user',
                'attributes' => [
                    'name' => 'Travis',
                    'email' => 'travis@mail.com',
                    'password' => 'password',
                    'password_confirmation' => 'password',
                ],
            ],
        ]);
        $response
            ->assertStatus(201)
            ->assertJson([
                'data' => [
                    'id' => 1,
                    'attributes' => [
                        'name' => 'Travis',
                        'email' => 'travis@mail.com',
                    ],
                ],
            ]);
    }

    public function testUserRegistrationIfAuthenticated() {
        Passport::actingAs(factory(\App\User::class)->create(), ['*']);

        $response = $this->postJson('/api/register', [
            'data' => [
                'type' => 'user',
                'attributes' => [
                    'name' => 'Travis',
                    'email' => 'travis@mail.com',
                    'password' => 'password',
                    'password_confirmation' => 'password',
                ],
            ],
        ]);
        $response->assertStatus(403);
    }

    public function testUserRegistrationExistingMail() {
        $email = 'travis@mail.com';
        $travis = factory(\App\User::class)->create(['email' => $email]);
        $response = $this->postJson('/api/register', [
            'data' => [
                'type' => 'user',
                'attributes' => [
                    'name' => 'Travis',
                    'email' => $email,
                    'password' => 'password',
                    'password_confirmation' => 'password',
                ],
            ],
        ]);
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors('data.attributes.email');
    }

    public function testUserRegistrationNoPasswordConfirmation() {
        $response = $this->postJson('/api/register', [
            'data' => [
                'type' => 'user',
                'attributes' => [
                    'name' => 'Travis',
                    'email' => 'travis@mail.com',
                    'password' => 'password',
                ],
            ],
        ]);
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors('data.attributes.password');
    }

    public function testUserRegistrationDifferentPasswordConfirmation() {
        $response = $this->postJson('/api/register', [
            'data' => [
                'type' => 'user',
                'attributes' => [
                    'name' => 'Travis',
                    'email' => 'travis@mail.com',
                    'password' => 'password',
                    'password_confirmation' => 'different',
                ],
            ],
        ]);
        $response
            ->assertStatus(422)
            ->assertJsonValidationErrors('data.attributes.password');
    }
}
