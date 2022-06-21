<?php

namespace App\Http\Controllers\Business;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Invitation;
use App\Models\Business\Business;
use App\Models\Business\UserBusiness;
use App\Mail\Business\InviteUser;
use Illuminate\Support\Facades\Redis;
use Mail;


class BusinessController extends Controller
{
    public function createBusiness(Request $request) 
    {
        $validator = Validator::make($request->all(),[
            'business_name' => 'required|string|max:255|unique:business',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 'error',
                'message' => 'the given data invalid',
                'errors' => $validator->errors()
            ], 422);       
        }

        $business = Business::create([
            'business_name' => $request->business_name,
        ]);

        // Create user business data
        $userBusiness = new UserBusiness();
        $userBusiness->userBusiness_user = Auth::id();
        $userBusiness->userBusiness_business = $business->id;
        $userBusiness->userBusiness_status = 'owner';
        $userBusiness->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Business Created',
        ]);
    }

    public function updateBusiness(Request $request, $id) 
    {
        $business = Business::find($id);
        if (!$business) {
            return response()->json([
                'status' => 'error',
                'message' => 'Business Not Found'
            ], 422);     
        }

        $data = $request->all();
        $validator = Validator::make($data,[
            'business_name' => 'required|string|max:255|unique:business',
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 'error',
                'message' => 'the given data invalid',
                'errors' => $validator->errors()
            ], 422);       
        }

        $business->fill($data);
        $business->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Business Updated',
        ]);
    }

    public function listBusiness(Request $request) 
    {
        $userBusiness = User::where('id', '=' , $request->user()->id)
                            ->with('business')
                            ->get();

        return response()->json([
            'status' => 'success',
            'data' => $userBusiness
        ]);
    }

    public function getInvitation(Request $request) 
    {
        $business = Business::find($request->query('business'));

        $invitation = Invitation::where('userInvitation_email', '=', $request->query('recipant'))
                                ->where('userInvitation_token', '=', $request->query('token'))
                                ->first();

        // Check business & email
        if (!$business && !$invitation) {
            return redirect()->intended(
                config('app.frontend_url').'/invalid-token'
            );
        }

        $token = sha1('Bloopy-BusinessInvitation-'.$request->input('recipant').'-'.date($invitation->expires_at));

        // Validate Token
        if (!hash_equals((string) $invitation->userInvitation_token, $token))
        {
            return redirect()->intended(
                config('app.frontend_url').'/invalid-token'
            );
        }

        // Check token expired
        if (now() >= $invitation->expires_at) {
            return redirect()->intended(
                config('app.frontend_url').'/token-expired'
            );
        }

        // Find user
        $user = User::where('email','=',$request->query('recipant'))
                    ->first();

        if (!$user) {
            $user = new User();
            $user->email = $request->query('recipant');
            $user->user_role = 'user';
            $user->save();
            $token = sha1('Bloopy-Invitation-'.$user->email.'-'.$user->created_at);            
        }

        // Check is user already in business
        $userBusiness = UserBusiness::where('userBusiness_business', '=', $business->id)
                                    ->where('userBusiness_user','=',$user->id)
                                    ->first();

        if ($userBusiness) {
            return redirect()->intended(
                config('app.frontend_url').'/user-invited'
            );
        }

        //Add user to userBusiness 
        $userBusiness->userBusiness_user = $user->id;
        $userBusiness->userBusiness_business = $business->id;
        $userBusiness->userBusiness_status = $request->query('status');
        $userBusiness->save();


        if (!$user) {
            return redirect()->intended(
                config('app.frontend_url').'/auth/register?email='.$user->email.'&token='.$token
            );
        }
        return redirect()->intended(
            config('app.frontend_url').'/user-invited'
        );
    }

    public function createInvitation(Request $request) 
    {
        $validator = Validator::make($request->all(),[
            'recipant_email' => 'required|string|email|max:255',
            'recipant_name'  => 'required|string|max:255',
            'business'       => 'required|exists:App\Models\Business\Business,id',
            'status'         => 'required|in:owner,employee'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 'error',
                'message' => 'the given data invalid',
                'errors' => $validator->errors()
            ], 422);       
        }


        $user = User::where('email','=',$request->recipant_email)->first();
        $business = Business::find($request->business);
        // Check if user already registered in business
        if ($user) {
            $userBusiness = UserBusiness::where('userBusiness_business','=',$business->id)
                                        ->where('userBusiness_user', '=', $user->id)
                                        ->first();
            if ($userBusiness) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'the given data invalid',
                    'errors' => [
                        'recipant_email' => 'the user is already registered in the business'
                    ]
                ], 422);       
            }
        }
        
        $sender   = $request->user()->user_name;
        $recipant = [
            'email' => $request->recipant_email,
            'name'  => $request->recipant_name,
        ];

        $expiry = date(now()->addDay(1));
        $token = sha1( (string) 'Bloopy-BusinessInvitation-'.$request->recipant_email.'-'.$expiry);

        $invitation = new Invitation();
        $invitation->userInvitation_email = $request->recipant_email;
        $invitation->userInvitation_token = $token;
        $invitation->expires_at = $expiry;
        $invitation->save();
        
        Mail::to($request->recipant_email)
            ->send(new InviteUser($business, $sender, $recipant, $request->status, $token));

        return response()->json([
            'status' => 'success',
            'message' => 'Invitation Link Sent',
        ]);
    }
}
