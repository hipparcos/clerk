<?php

use Illuminate\Database\Seeder;
use \Carbon\Carbon;

class OauthClientsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $now = Carbon::now()->format('Y-m-d H:i:s');
        DB::table('oauth_clients')->insert([
            'id' => 1,
            'name' => 'clerk-web',
            'secret' => 'DvYKWsQPGXUPrRH41PsHtrMgtMMwfalJ0BjsoVhF',
            'redirect' => env('APP_URL', ''),
            'personal_access_client' => 0,
            'password_client' => 1,
            'revoked' => 0,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }
}
