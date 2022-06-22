<?php

namespace App\Http\Controllers\Bank;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bank\Bank;
use Xendit\Customers;
use App\Models\User;


class UserBankController extends Controller
{
    public function linkBank(Request $request) 
    {
        // $data = $request->all();
        // $validate = Validator::make($data, [
        //     'bank_code' => 'required|string|exists:bank,bank_code',
        //     'bank_number' => 'required|integer'
        // ]);

        // if ($validate->fails()) {
        //     return response()->json([
        //         'status' => 'error',
        //         'message' => $validate->errors()
        //     ], 402);
        // }

        // check customer in xendit
        $user = User::find($request->user()->id);

        if (!$user->user_xenditCustomerId)
        {
            if (!$user->user_phone) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'User phone number not found',
                ], 404);
            }
            $response->json();
            $user->user_xenditCustomerId = $response['id'];
            $user->save();
        }

        return response()->json([
            'status'  => 'success',
            'message' => $user
        ]);
    }
}
