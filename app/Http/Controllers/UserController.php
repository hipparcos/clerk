<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Auth\Events\Registered;
use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\User\Register as RegisterRequest;
use App\Http\Resources\User as UserResource;

class UserController extends Controller
{
    /**
     * Handle a registration request for the application.
     *
     * @override
     * @param  \App\Http\Requests\User\Register  $request
     * @return \Illuminate\Http\Response
     */
    public function register(RegisterRequest $request)
    {
        $data = $request->validated()['data']['attributes'];

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
        ]);
        Auth::guard()->login($user);

        return (new UserResource($user))->response(201)
            ->header('Location', route('profile'));
    }
}
