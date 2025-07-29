<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use Google_Client;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

    class GoogleAuthController extends Controller
{
    public function google(Request $request)
    {
        $client = new \Google_Client(['client_id' => env('GOOGLE_CLIENT_ID')]);
        $payload = $client->verifyIdToken($request->token);

        if (!$payload) {
            return response()->json(['error' => 'Invalid Token'], 401);
        }

        // Extract user info from Google
        $googleId = $payload['sub'];
        $email = $payload['email'];
        $name = $payload['name'];

        // Find or create user
        $user = User::firstOrCreate(
            ['email' => $email],
            ['name' => $name, 'google_id' => $googleId, 'password' => bcrypt(str()->random(16))]
        );

        // Generate JWT
        $token = JWTAuth::fromUser($user);

        return response()->json(['token' => $token]);
    }
}
