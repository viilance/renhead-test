<?php

namespace App\Providers;

// use Illuminate\Support\Facades\Gate;
use App\Models\Approval;
use App\Models\Professor;
use App\Models\Trader;
use App\Policies\ApprovalPolicy;
use App\Policies\ProfessorPolicy;
use App\Policies\TraderPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The model to policy mappings for the application.
     *
     * @var array<class-string, class-string>
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
        Approval::class => ApprovalPolicy::class,
        Professor::class => ProfessorPolicy::class,
        Trader::class => TraderPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     */
    public function boot(): void
    {
        $this->registerPolicies();
    }
}
