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
        
        $updated_field = [
            'bio' => $request->bio,
            'name' => $request->name,
            'phone' => $request->phone,
        ];


        if($request->password) {
           $updated_field['password'] = bcrypt($request->password);
        }

        if($request->avatar) {
           $updated_field['avatar'] = $request->avatar;
        }

        // Update user
        $user->update($updated_field);
      
        return $user;
    }
}
