<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\URL;

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
        if ($this->app->environment('production') || isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https') {
            URL::forceScheme('https');
        }

        try {
            if (\Illuminate\Support\Facades\Schema::hasTable('users') && !\Illuminate\Support\Facades\Schema::hasColumn('users', 'role')) {
                \Illuminate\Support\Facades\Schema::table('users', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->string('role')->default('teknisi');
                });
            }

            if (\Illuminate\Support\Facades\Schema::hasTable('job_orders') && !\Illuminate\Support\Facades\Schema::hasColumn('job_orders', 'user_id')) {
                \Illuminate\Support\Facades\Schema::table('job_orders', function (\Illuminate\Database\Schema\Blueprint $table) {
                    $table->foreignId('user_id')->nullable()->constrained('users')->nullOnDelete();
                });
            }
        } catch (\Throwable $e) {
            // Ignore database boot check exceptions
        }
    }
}
