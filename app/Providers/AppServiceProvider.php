<?php

namespace App\Providers;

use App\Models\Professor;
use App\Models\Trader;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Validator::extend('employee_exists', function ($attribute, $value, $parameters, $validator) {
            $data = $validator->getData();
            $employeeType = $data['employee_type'] ?? null;

            if ($employeeType === 'professor') {
                return Professor::query()->find($value) !== null;
            } elseif ($employeeType === 'trader') {
                return Trader::query()->find($value) !== null;
            }

            return false;
        }, 'The specified employee does not exist.');
    }
}
