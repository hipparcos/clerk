<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\DatabaseMigrations;

class UserRegistrationTest extends TestCase
{
    use DatabaseMigrations;

    public function testUserRegistration() {
        $response = $this->post('/api/auth/register', [
            'name' => 'Travis',
            'email' => 'travis@mail.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        $response
            ->assertStatus(201)
            ->assertJson(['user' => [
            'id' => 1,
            'name' => 'Travis',
            'email' => 'travis@mail.com',
            ]]);
    }

    public function testUserRegistrationExistingMail() {
        $email = 'travis@mail.com';
        $travis = factory(\App\User::class)->create(['email' => $email]);
        $response = $this->post('/api/auth/register', [
            'name' => 'Travis',
            'email' => $email,
            'password' => 'password',
            'password_confirmation' => 'password',
        ]);
        $response
            ->assertStatus(401)
            ->assertJsonValidationErrors('email');
    }

    public function testUserRegistrationNoPasswordConfirmation() {
        $response = $this->post('/api/auth/register', [
            'name' => 'Travis',
            'email' => 'travis@mail.com',
            'password' => 'password',
        ]);
        $response
            ->assertStatus(401)
            ->assertJsonValidationErrors('password');
    }

    public function testUserRegistrationDifferentPasswordConfirmation() {
        $response = $this->post('/api/auth/register', [
            'name' => 'Travis',
            'email' => 'travis@mail.com',
            'password' => 'password',
            'password_confirmation' => 'test',
        ]);
        $response
            ->assertStatus(401)
            ->assertJsonValidationErrors('password');
    }
}
