<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use GuzzleHttp\Client;

class UserController extends Controller
{
    public function index(Request $request) {

        return $request->user();
    }

    public function update(Request $request, $id) {
        // Get current user
        $user = $request->user();

        // get and check avatar image
        $file = $request->file('avatar');
        $avatar = $user->avatar;

        if($file) {
            $request->validate([
                'avatar' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);

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

            $avatar = $data["url"];
        }
        
        $updated_field = [
            'avatar' => $avatar,
            'bio' => $request->bio,
            'name' => $request->name,
            'phone' => $request->phone,
        ];


        if($request->password) {
           $updated_field['password'] = bcrypt($request->password);
        }

        // Update user
        $user->update($updated_field);
      
        return $user;
    }
}
