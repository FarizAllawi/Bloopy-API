<?php

namespace App\Http\Controllers;

use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Notifications\VerificationUser;
use App\Notifications\RegisterUser;
use App\Mail\AfterRegister;
use App\Models\User;
use Mail;

class AuthController extends Controller
{

    public function user(Request $request) 
    {
        $user = auth()->user();
        return response()->json([
            'status' => 'success',
            'data' => $user
        ]);
    }

    public function login(Request $request) 
    {
        $validator = Validator::make($request->all(),[
            'email' => 'required|string|email',
            'password' => 'required|string'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 'error',
                'message' => 'Data yang diberikan tidak valid',
                'errors' => $validator->errors()
            ], 422);       
        }

        if (!$this->ensureIsNotRateLimited($request));
        else return $this->ensureIsNotRateLimited($request);

        if (! Auth::attempt($request->only('email', 'password'), $request->remember)) {
            RateLimiter::hit($this->throttleKey($request), $seconds=3600);
            return response()->json([
                'status' => 'error',
                'message' => 'the given data invalid'
            ], 422);
        }

        RateLimiter::clear($this->throttleKey($request));
        
        /**
         * We are authenticating a request from our frontend
         */
        if (EnsureFrontendRequestsAreStateful::fromFrontend($request)){
            $request->session()->regenerate();
            return response()->json([
                'status' => 'success',
                'message' => 'User Logged in'
            ]);
        }
        
        // Use token authentication.
        $user = User::find(Auth::id());
        return response()->json([
            'status' => 'success',
            'message' => 'User Logged in',
            'data' => [
                'token' => $user->createToken('Auth Token : '.$user->email)->accessToken
            ]
        ]);
    }

    public function register(Request $request) 
    {
        $validator = Validator::make($request->all(),[
            'name' => 'required|string|max:255',
            'email' => $request->query('email') ? 'required|string|email|max:255' : 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 'error',
                'message' => 'the given data invalid',
                'errors' => $validator->errors()
            ], 422);       
        }
        
        $user = new User();
        // Register user from invitation
        if ($request->query('email') && $request->query('token')) {
            // Check user in database
            $user = User::where('email', '=', $request->query('email'))
                        ->first();

            // Validate Token
            $token = sha1('Bloopy-Invitation-'.$user->email.'-'.$user->created_at);
            if (!hash_equals((string) $request->query('token'), $token)) {
                // -- Token not valid --
                // check the request email first
                
                $email = User::where('email','=',$request->email)->first();
                if ($email) {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'the given data invalid',
                        'errors' => [
                           'email' => 'The email is already taken'
                        ]
                    ], 422); 
                } 

                $user = $user = User::create([
                    'user_name' => $request->name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                    'user_role' => 'user',
                 ]);

                 event(new Registered($user));
            }

            // Token Is valid
            $user->user_name = $request->name;
            $user->password = Hash::make($request->password);
            // If request email match with query email
            if ($request->email === $user->email) {
                $user->markEmailAsVerified();
                event(new Verified($user));
                Mail::to($user->email)->send(new AfterRegister($user));
            }
            else {
                $user->email = $request->email;
                event(new Registered($user));
            }
            $user->save();
        
        } else {
            $user->user_name = $request->name;
            $user->email = $request->email;
            $user->password = Hash::make($request->password);
            $user->user_role = 'user';
            $user->save();
        }

         /**
         * We are authenticating a request from our frontend
         */
        if (EnsureFrontendRequestsAreStateful::fromFrontend($request)){
            $emailNotification = $this->emailNotification('link', $user);
            Auth::login($user);
            return response()->json([
                'status' => 'success',
                'message' => 'User Logged in'
            ]);
        }
        
        // Use token authentication.
        $emailNotification = $this->emailNotification('code', $user, $request->query('platform') , $request->query('app'));
        return response()->json([
            'status' => 'success',
            'message' => 'Email Verfication Sent',
            'data' => [
                'user' => [
                    'user_id' => $user->id,
                    'verification_token' => $emailNotification->original['data']['token']
                ],
                'token' => $user->createToken('Auth Token : '.$user->email)->accessToken,
                
            ],
        ]);

    }

    public function logout(Request $request) 
    {
        // Frontend Logout
        if (EnsureFrontendRequestsAreStateful::fromFrontend(request())) 
        {
            Auth::guard('web')->logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
        } else {
            // token logout.
            $accessToken = auth()->user()->token();
            $token= $request->user()->tokens->find($accessToken);
            $token->revoke();
        }
        
        return response()->json([
            'status'  => 'success',
            'message' => 'You have successfully logged out and the token was successfully deleted',
        ]);
    }

    public function google() 
    {
        return response()->json([
            'status' => 'success', 
            'data' => Socialite::with('google')->stateless()->redirect()->getTargetUrl()
        ]);
    }

    public function handleGoogleCallback() 
    {
        $callback = Socialite::driver('google')->stateless()->user();
        $data = [
            'user_name' => $callback->getName(),
            'user_username' => $callback->getNickname(),
            'email' => $callback->getEmail(),
            'user_photo' => $callback->getAvatar(),
            'user_role' => 'user'
        ];

        // If email not exist in database then create user
        $user = User::whereEmail($data['email'])->first();
        if (!$user) {
            $user = User::create($data);
            Mail::to($user->email)->send(new AfterRegister($user));
        }
        Auth::login($user, true);
        // $token = $user->createToken('Auth Token : '.$data['email'])->accessToken;
        return  redirect()->intended(config('app.frontend_url'));
    }

    public function emailNotification($type = null, $user = null, $platform = null, $app = null) 
    {
        $request = request();
        $user    = $user ? $user : $request->user();
        $type    = $request->query('type') ? $request->query('type') : $type;

        if ($user->hasVerifiedEmail() ) 
        {
            return response()->json([
                'status' => 'success',
                'message' => 'The user has verified the email'
            ]);
        }

        if ( $type === 'code') 
        {
            $platform = $request->query('platform') ? $request->query('platform') : $platform;
            $app      = $request->query('app') ? $request->query('app') : $app;
            $code     = collect([1,2,3,4,5,6,7,8,9,0])->random(4);
            $code     = $code[0].$code[1].$code[2].$code[3];
            $token    = sha1('BloopyVerivicationCode-'.$user->email.'-'.$code);
            
            $user->notify(new VerificationUser($type, $user, $code, $platform, $app));
            return response()->json([
                'status' => 'success',
                'message' => 'Code verification sent',
                'data' => [
                    'token' => $token
                ]
            ]);
        }

        if ($type === 'link') 
        {
            $user->notify(new VerificationUser($type, $user, '', '', ''));
        }
        
        return response()->json([
            'status' => 'success',
            'message' => 'verification link sent',
        ]);

    }

    public function emailVerify(Request $request, $id, $token) 
    {
        $user = User::find($id);
        if ($request->query('type') && $request->query('type') === 'link') {
            if (!$user) {
                return $this->redirectUsers($request->query('app'), '/user-not-found');
            }

            // Validate token
            if (! hash_equals((string) $token, sha1($user->email))) {
                return $this->redirectUsers($request->query('app'), '/token-invalid');
            } 
        }
        
        if ($request->query('type') && $request->query('type') === 'code') {
            if (!$user) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'User not found, please register'
                ], 422);       
            }

            $validator = Validator::make($request->all(),[
                'code' => 'required|integer',
            ]);
    
            if($validator->fails()){
                return response()->json([
                    'status' => 'error',
                    'message' => 'the given data invalid',
                    'errors' => $validator->errors()
                ], 422);       
            }

            // Validate Token
            $tokenFormula = sha1('BloopyVerivicationCode-'.$user->email.'-'.$request->code);
            if (!hash_equals((string) $token , $tokenFormula)) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'invalid code',
                ], 422);  
            }
        }
        
        
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));
        }

        $administator = User::where('user_role','=','admin')->get();
        // Notify Administrator
        Notification::send($administator, new RegisterUser($user));

        if ($request->query('type') && $request->query('type') === 'code') {
            return response()->json([
                'status' => 'success',
                'message' => 'User Verified',
            ]);     
        }

        return $this->redirectUsers($request->query('app'), '/email-verified');
    }


    public function redirectUsers($app, $redirect) 
    {
        return $app === 'web' ? redirect()->intended(
                                    config('app.frontend_url').$redirect
                                )
                              : redirect()->away($redirect);
    }


    /**
     * Ensure the login request is not rate limited.
     *
     * @return void
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function ensureIsNotRateLimited($request)
    {
        if (! RateLimiter::tooManyAttempts($this->throttleKey($request), 5)) {
            return false;
        }

        event(new Lockout($request));

        $seconds = RateLimiter::availableIn($this->throttleKey($request));
        return response()->json([
            'status' => 'error',
            'message' => 'Too many login attemtps, Please try again in '.ceil($seconds / 60).' minutes',
        ], 422);     
    }

    /**
     * Get the rate limiting throttle key for the request.
     *
     * @return string
     */
    public function throttleKey($request)
    {
        return Str::lower($request->email).'|'.$request->ip();
    }

}
