<?php

namespace AlexBuckham\CloudflareImagesLaravel;

use Illuminate\Support\ServiceProvider;

class CloudflareImagesProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        // Publish configuration file
        if ($this->app->runningInConsole()) {
            $this->publishes([
                __DIR__ . '/config/cloudflare-images.php' => config_path('cloudflare-images.php'),
            ], 'cloudflare-images-config');
        }

        // Merge default configuration
        $this->mergeConfigFrom(
            __DIR__ . '/config/cloudflare-images.php',
            'cloudflare-images'
        );
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        // Register the CloudflareImages service as a singleton
        $this->app->singleton(CloudflareImages::class, function ($app) {
            return new CloudflareImages(
                config('cloudflare-images.account_id'),
                config('cloudflare-images.token'),
                config('cloudflare-images.key'),
                config('cloudflare-images.delivery_url')
            );
        });

        // Register the facade alias
        $this->app->alias(CloudflareImages::class, 'cloudflare-images');
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array
     */
    public function provides()
    {
        return [
            CloudflareImages::class,
            'cloudflare-images',
        ];
    }
}
