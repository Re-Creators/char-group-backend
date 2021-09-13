<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use GuzzleHttp\Exception\ClientException;
use Illuminate\Support\Facades\Hash;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function register(Request $request) {
        $fields = $request->validate([
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6'
        ]);
        $user = User::create([
            'email' => $fields['email'],
            'password' => bcrypt($fields['password']),
            'avatar' => 'https://t4.ftcdn.net/jpg/02/15/84/43/360_F_215844325_ttX9YiIIyeaR7Ne6EaLLjMAmy4GvPC69.webp'
        ]);
        
        $user->update([
            'name' => 'User#' . $user->id
        ]);
        
        $token = $user->createToken('my-app-token')->plainTextToken;
        return response([
            'token' => $token,
        ], 201);
    }


    public function login(Request $request) {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
    
        $user = User::where('email', $request->email)->first();
        if(!$user || !Hash::check($request->password, $user->password)) {
            return response([
                'message' => ['These credentials do not match out records']
            ], 404);
        }
    
        $token = $user->createToken('my-app-token')->plainTextToken;
        
        return response([
            'token' => $token
        ], 201);
    }

    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();

        return response([
            'message' => 'Log out successfully'
        ]);
    }

    public function redirectToProvider($provider) {
        $validated = $this->validateProvider($provider);

        if(!is_null($validated)) {
            return $validated;
        }

        $url = Socialite::driver($provider)->stateless()->redirect()->getTargetUrl();

        return response([
            'redirect' => $url
        ]);
    }

    public function handlerProviderCallback($provider) {
        $validated = $this->validateProvider($provider);
        if(!is_null($validated)) {
            return $validated;
        }
        
        try{
            $user = Socialite::driver($provider)->stateless()->user();
        } catch(ClientException $exception) {
            return response()->json([
                'error' => 'Invalid credentials provided.'
            ], 422);
        }

        $userCreated = User::firstOrCreate(
            [
                'email' => $user->getEmail()
            ],
            [
                'email_verified_at' => now(),
                'name' => $user->getName(),
                'avatar' => $user->getAvatar(),
                'status' => true
            ]
        );

        $userCreated->providers()->updateOrCreate(
            [
                'provider' => $provider,
                'provider_id' => $user->getId(),
            ],
        );

        $token = $userCreated->createToken('my-app-token')->plainTextToken;

        return view('callback',[
            'token' => $token
        ]);
    }


    protected function validateProvider($provider) {
        if(!in_array($provider, ['github', 'facebook', 'google'])){
            return response()->json([
                'error' => 'Please login using facebook, github, or google'
            ], 422);   
        }
    }
}
