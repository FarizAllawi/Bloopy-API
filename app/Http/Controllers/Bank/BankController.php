<?php

namespace App\Http\Controllers\Bank;

use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Http;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Bank\Bank;
use App\Models\User;

class BankController extends Controller
{
    public function generateBank() 
    {
        $response = HTTP::withBasicAuth(env(""))
                        ->get('https://api.xendit.co/available_disbursements_banks');
        
        
        foreach($response->json() as $response) {
            $bank = Bank::where('bank_code','=',$response['code'])->withTrashed()->first();
            if (!$bank) {
                Bank::create([
                    'bank_name' => $response['name'],
                    'bank_code' => $response['code'],
                    'bank_can_disburse' => $response['can_disburse'],
                    'bank_can_name_validate' => $response['can_name_validate']
                ]);
            }
        }

        return response()->json([
            'status'  => 'success',
            'message' => 'Bank generated' ,
        ]);
    }

    public function listBank() 
    {
        return response()->json([
            'status' => 'sucess',
            'data' => Bank::all()
        ]);
    }

    public function listVa() 
    {
        return response()->json([
            'status' => 'sucess',
            'data' => VirtualAccounts::getVABanks()
        ]);
    }

    public function deleteBank($id) 
    {
        $bank = Bank::find($id);
        if (!$bank) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Bank not found' ,
            ], 422);
        }

        $bank->delete();
        return response()->json([
            'status'  => 'success',
            'message' => 'Bank deleted' ,
        ]);
    }

   
}
