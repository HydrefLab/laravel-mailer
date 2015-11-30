<?php namespace DeSmart\Mailer\ServiceProvider;

use DeSmart\Mailer\Mandrill\Console\MandrillTemplatesSeedCommand;
use DeSmart\Mailer\Mandrill\Mailer;
use Illuminate\Support\ServiceProvider;

class MandrillServiceProvider extends ServiceProvider
{
    /**
     * @return void
     */
    public function boot()
    {
        $this->loadConfiguration();
    }

    /**
     * @return void
     */
    public function register()
    {
        $this->app->bind(\DeSmart\Mailer\MailerInterface::class, function () {
            return new Mailer(
                $this->app->make(\Weblee\Mandrill\Mail::class),
                $this->app['config']['mail']['from']['address'],
                $this->app['config']['mail']['from']['name']
            );
        });

        $this->app->bind(\DeSmart\Mailer\Mandrill\Console\MandrillTemplatesSeedCommand::class, function () {
            return new MandrillTemplatesSeedCommand(
                $this->app->make(\Weblee\Mandrill\Mail::class),
                config('mandrill-templates')
            );
        });
    }

    /**
     * @return void
     */
    protected function loadConfiguration()
    {
        $configPath = __DIR__ . '/../config/mandrill-templates.php';;

        $this->publishes([$configPath => config_path('mandrill-templates.php'),]);
        $this->mergeConfigFrom($configPath, 'mandrill-templates');
    }
} 