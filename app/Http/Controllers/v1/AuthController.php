<?php

namespace App\Http\Controllers\v1;

use Laravel\Sanctum\Http\Middleware\EnsureFrontendRequestsAreStateful;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Validator;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Events\Verified;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Mail\AfterRegister;
use App\Models\User;
use Mail;

class AuthController extends Controller
{

    public function user(Request $request) 
    {
        return response()->json([
            'status' => 'success',
            'data' => $request->user()
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
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8'
        ]);

        if($validator->fails()){
            return response()->json([
                'status' => 'error',
                'message' => 'the given data invalid',
                'errors' => $validator->errors()
            ], 422);       
        }

        $user = User::create([
            'user_name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'user_role' => 'user',
        ]);

        event(new Registered($user));
         /**
         * We are authenticating a request from our frontend
         */
        if (EnsureFrontendRequestsAreStateful::fromFrontend($request)){
            Auth::login($user);
            return response()->json([
                'status' => 'success',
                'message' => 'User Logged in'
            ]);
        }
        
        // Use token authentication.
        return response()->json([
            'status' => 'success',
            'message' => 'Email Verfication Sent',
            'data' => [
                'token' => $user->createToken('Auth Token : '.$user->email)->accessToken
            ]
        ]);

    }

    public function logout() 
    {
        // Frontend Logout
        if (EnsureFrontendRequestsAreStateful::fromFrontend(request())) {
            Auth::guard('web')->logout();
            request()->session()->invalidate();
            request()->session()->regenerateToken();
        }
        // token logout.
        $user = Auth::user();
        $user->token->delete(); 
        return response()->json([
            'status'  => 'success',
            'message' => 'You have successfully logged out and the token was successfully deleted'
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

    public function emailVerify(Request $request, $id, $hash) 
    {
        $user = User::find($id);

        if (!$user) {
            return redirect()->intended(
                config('app.frontend_url').'/user-not-found'
            );
        }

        if (! hash_equals((string) $hash, sha1($user->email))) {
            return redirect()->intended(
                config('app.frontend_url').'/user-not-found'
            );
        }
        
        if (!$user->hasVerifiedEmail()) {
            $user->markEmailAsVerified();
            event(new Verified($user));
        }
        
        return redirect()->intended(
            config('app.frontend_url').'/email-verified'
        );
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
