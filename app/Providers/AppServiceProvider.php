<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Kreait\Firebase\Factory;
use Kreait\Firebase\Contract\Auth as FirebaseAuthContract; // Pastikan ini di-import
use Illuminate\Support\Facades\Log; // Untuk logging jika terjadi error

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(FirebaseAuthContract::class, function ($app) {
            $credentialsPath = config('services.firebase.credentials_path');

            if (!$credentialsPath) {
                $errorMessage = 'Firebase credentials path (services.firebase.credentials_path) is not configured in config/services.php.';
                Log::error($errorMessage);
                // Melempar exception akan menghentikan aplikasi dan memberikan error yang jelas
                throw new \InvalidArgumentException($errorMessage);
            }
            
            if (!file_exists($credentialsPath)) {
                $errorMessage = 'Firebase credentials file not found at specified path: ' . $credentialsPath . '. Please verify the path and ensure the file exists.';
                Log::error($errorMessage);
                throw new \InvalidArgumentException($errorMessage);
            }

            try {
                $factory = (new Factory)->withServiceAccount($credentialsPath);
                return $factory->createAuth();
            } catch (\Exception $e) {
                $errorMessage = 'Failed to initialize Firebase Auth with credentials from ' . $credentialsPath . '. Error: ' . $e->getMessage();
                Log::error($errorMessage);
                // Melempar exception agar masalahnya jelas saat development
                throw new \RuntimeException($errorMessage, 0, $e);
            }
        });

        // Binding lain yang mungkin sudah ada di sini...
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}