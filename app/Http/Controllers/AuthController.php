<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\RegisterUser;
use App\Http\Resources\User as UserResource;

class AuthController extends Controller
{
    /**
     * Handle a registration request for the application.
     *
     * @override
     * @param  \App\Http\Requests\RegisterUser  $request
     * @return \Illuminate\Http\Response
     */
    public function register(RegisterUser $request)
    {
        $data = $request->validated()['data']['attributes'];

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
        Auth::guard()->login($user);

        return response()->json([
                'data' => new UserResource($user),
            ], 201)
            ->withHeaders([
                'Location', route('profile')
            ]);
    }
}
