<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ChatController;
use App\Http\Controllers\UserController;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
use Illuminate\Support\Facades\Broadcast;
use Symfony\Component\Console\Input\Input;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Broadcast Authorization
Broadcast::routes(['middleware' => ['auth:sanctum']]);

// Sanctum Routes
Route::middleware(['auth:sanctum'])->group(function() {
    // User
    Route::get('/user', [UserController::class, 'index']);
    Route::PUT('/user/{id}', [UserController::class, 'update']);

    Route::post('/logout', [AuthController::class, 'logout']);

    // Channel
    Route::get('/chat/channels', [ChatController::class, 'channels']);
    Route::post('/chat/channel', [ChatController::class, 'newChannel']);

    // Messages
    Route::get('/chat/{channelId}/messages', [ChatController::class, 'messages']);
    Route::post('/chat/{channelId}/message', [ChatController::class, 'newMessage']);

    // Member
    Route::get('/chat/{channelId}/members', [ChatController::class, 'members']);
    Route::post('/chat/{channelId}/member', [ChatController::class, 'newMember']);
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);

Route::post('/debug', function (Request $request) {
    $file = $request->file('avatar');

    $file->store('public');

    $image = fopen('../storage/app/public/' . $file->hashName(), 'r');

    $client = new Client([
        'base_uri' => 'https://api.cloudinary.com/v1_1/'
    ]);

    $response = $client->request('POST', 're-creators79/image/upload', [
        'multipart' => [
            [
                'name' => 'upload_preset',
                'contents' => 'auth-preset'
            ],
            [
                'name' => 'file',
                'contents' => $image
            ]
        ]
    ]);
    $data = json_decode($response->getBody(), true);

    return $data["url"];
});

// Socialite Routes
Route::get('/auth/{provider}/redirect',[AuthController::class, 'redirectToProvider']);
Route::get('auth/{provider}/callback', [AuthController::class, 'handlerProviderCallback']);

