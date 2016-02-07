<?php namespace DeSmart\Mailer\ServiceProvider;

use DeSmart\Mailer\SendGrid\Mailer;
use Illuminate\Support\ServiceProvider;

class SendGridServiceProvider extends ServiceProvider
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
            return new Mailer(
                new \SendGrid($this->app['config']['services']['sendgrid']['apikey']),
                $this->app->make(\Illuminate\Contracts\Filesystem\Filesystem::class),
                $this->app['config']['mail']['from']['address'],
                $this->app['config']['mail']['from']['name']
            );
        });
    }
} 