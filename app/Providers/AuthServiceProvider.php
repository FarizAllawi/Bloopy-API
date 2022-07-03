<?php

namespace App\Providers;

use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        ResetPassword::createUrlUsing(function ($notifiable, $token) {
            return config('app.frontend_url')."/password-reset/$token?email={$notifiable->getEmailForPasswordReset()}";
        });

        Passport::routes();
        Passport::tokensCan([
            'bloopy-owner' => 'Manage Company Data and has all feature on Manager and employee',
            'bloopy-works-c-level' => 'Scope Description',
            'bloopy-works-middle-management' => 'Scope Description',
            'bloopy-works-first-level-management' => 'Scope Description',
            'bloopy-works-intermediate-or-experienced' => 'Scope Description',
            'bloopy-works-entry-level' => 'Scope Description',
        ]);
    }
}
