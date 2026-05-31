<?php

namespace Ram\EmailSandbox;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Config;
use Ram\EmailSandbox\Transport\EmailSandboxTransport;

class EmailSandboxServiceProvider extends ServiceProvider
{
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/../config/email-sandbox.php',
            'email-sandbox'
        );
    }

    public function boot()
    {
        // Register Mailer
        Config::set('mail.mailers.email-sandbox', [
            'transport' => 'email-sandbox',
        ]);

        Mail::extend('email-sandbox', function () {
            return new EmailSandboxTransport();
        });

        // Load package components
        $this->loadRoutesFrom(__DIR__.'/routes/web.php');
        $this->loadViewsFrom(__DIR__.'/resources/views', 'email-sandbox');
        $this->loadMigrationsFrom(__DIR__.'/database/migrations');

        // Publish config
        $this->publishes([
            __DIR__.'/../config/email-sandbox.php' =>
                config_path('email-sandbox.php'),
        ], 'email-sandbox-config');
    }
}