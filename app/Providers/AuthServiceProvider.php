<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use App\Models\Risk;
use App\Policies\RiskPolicy;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        Risk::class => RiskPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();

        // Jika mau pakai Gate tambahan, bisa didefinisikan di sini.
        // Contoh:
        // Gate::define('approve-risk', [RiskPolicy::class, 'approve']);
    }
}
