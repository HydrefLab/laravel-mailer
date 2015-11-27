<?php namespace DeSmart\Mailer\ServiceProvider;

use DeSmart\Mailer\Mandrill\Mailer;
use Illuminate\Support\ServiceProvider;

class MandrillServiceProvider extends ServiceProvider
{
    public function boot()
    {
        //
    }

    /**
     * @return void
     */
    public function register()
    {
        $this->app->bind(\DeSmart\Mailer\MailerInterface::class, function () {
            new Mailer(
                $this->app->make(\Weblee\Mandrill\Mail::class),
                $this->app['config']['mail']['from']['address'],
                $this->app['config']['mail']['from']['name']
            );
        });
    }
} 