<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

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
            //Validate Image
            $request->validate([
                'avatar' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);

            $name = '/images/' . uniqid() . '.' . $file->extension();
            $file->storePubliclyAs('public', $name);
            $avatar = url($name);
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
