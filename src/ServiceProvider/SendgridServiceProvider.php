<?php namespace DeSmart\Mailer\ServiceProvider;

use Illuminate\Support\ServiceProvider;

class SendgridServiceProvider extends ServiceProvider
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
        $this->app->bind(\DeSmart\Mailer\MailerInterface::class, \DeSmart\Mailer\Sendgrid\Mailer::class);
    }
} 