<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
        
        Gate::define('admin', function ($user) {
            if ($user->isAdmin == 1) {
               return true;
            } else {
                return false;
            }
        });
        Gate::define('debug', function ($user) {
            if ($user->name == 'Challet Yann') {
               return true;
            } else {
                return false;
            }
        });
        //
    }
}
