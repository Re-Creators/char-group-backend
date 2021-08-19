<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\UserController;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Http\Request;
use GuzzleHttp\Client;
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

// Sanctum Routes
Route::middleware(['auth:sanctum'])->group(function() {
    Route::get('/user', [UserController::class, 'index']);
    Route::PUT('/user/{id}', [UserController::class, 'update']);
    Route::post('/logout', [AuthController::class, 'logout']);
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

