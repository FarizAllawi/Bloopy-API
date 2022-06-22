<?php

namespace App\Http\Controllers\User;

use Illuminate\Support\Facades\Validator;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\User;

class UserController extends Controller
{
    public function getUserRealtimeData()
    {
        return response()->json([
            'status' => 'success',
            'data' => [
                'notifications' => auth()->user()->notifications
            ]
        ]);
    }

    public function update(Request $request) 
    {
        $user = User::find($request->user()->id);
        $rules = [
            'user_name' => 'string',
            'user_username' => 'string|regex:/^\S*$/u|unique:users,user_username,'.$user->id,
            'email' => 'email|uniques:users,email,'.$user->id,
            'password' => 'string|min:8',
            'user_phone' => 'string',
            'user_gender' => 'string|in:male,female,other',
            'user_birthPlace' => 'string',
            'user_birthDate' => 'date',
            'user_identityType' => 'string|in:id-card,passport',
            'user_identityNumber' => 'string',
            'user_identityExpiryDate' => 'date'
        ];
        
        $data = $request->all();
        $validate = Validator::make($data, $rules);

        if ($validate->fails()) {
            return response()->json([
                'status' => 'error',
                'message' => $validate->errors()
            ], 400);
        }

        $user->fill($data);
        $user->save();
        return response()->json([
            'status' => 'success',
            'message' => 'User updated sucessfully',
            'data' => $user
        ]);
    }
}
